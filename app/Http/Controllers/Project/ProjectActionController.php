<?php
/**
 * CODE
 * ProjectAction Controller
 */

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ApiController;
use App\Models\DetailProject;
use App\Models\DetailProjectProcessProject;
use App\Models\PhasesProcess;
use App\Models\Process;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectActionController extends ApiController
{

    /**
     * @param Request $request
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function startProject(Request $request, Project $project, Process $process): JsonResponse
    {

        $this->validate($request, [
            'comments' => 'nullable|string',
        ]);
        // phpcs:ignore
        if ($project->user_id !== Auth::id()) {
            return $this->errorResponse('this user not create project', 409);
        }

        $currentProcess = $this->getCurrentProcess($project, $process);

        if (is_bool($currentProcess)) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']', 409);
        }

        $detailProcesses = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)->first();

        if (!empty($detailProcesses)) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] del proyecto [' . $project->name . '] ya esta iniciado consulte el formulario en curso', 409);
        }

        $currentPhase = [];
        foreach ($process->config['order_phases'] as $phase) {
            if ($phase['order'] === 1) {
                $currentPhase = $phase;
            }
        }

        $phase = PhasesProcess::findOrFail($currentPhase['phase']['id']);
        $currentInvolved = [];
        foreach ($project->config as $config) {
            if ($config['process']['id'] === $currentProcess->id) {
                foreach ($config['phases'] as $phase) {
                    if ($phase['phase']['id'] === $currentPhase['phase']['id']) {
                        $currentInvolved = $phase['involved'];
                    }
                }
            }
        }


        $detailProject = new DetailProject();
        $detailProject->comments = $request->get('comments');
        $detailProject->finished = DetailProject::$CURRENT;
        // phpcs:ignore
        $detailProject->phases_process_id = $phase->id;
        // phpcs:ignore
        $detailProject->form_data = ['form' => $phase->form, 'rules' => $currentInvolved];
        $detailProject->save();


        $detailProject->processProject()->attach($currentProcess->pivot->id);
        foreach ($project->process as $processes) {
            //TODO verificar la relación para acceder a través de los modelos
            $detailProcesses = DetailProjectProcessProject::where('process_project_id', $processes->pivot->id)->get();
            foreach ($detailProcesses as $dProcess) {
                $dProcess->detailProject;
            }
            $processes->detailProcess = $detailProcesses;
        }

        return $this->showList($project);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function nextPhaseProcess(Request $request, Project $project, Process $process): JsonResponse
    {
        $this->validate($request, [
            'comments' => 'nullable|string',
        ]);

        if ($this->validProjectProcess($project, $process)) {
            // phpcs:ignore
            return $this->errorResponse($this->validProjectProcess($project, $process), 409);
        }


        if ($this->newDetailPhaseProcess($project, $process, $request->get('comments'))) {

            return $this->showList($project);
        } else {

            return $this->errorResponse('this phase not have next', 409);
        }

    }

    /**
     * @param Request $request
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function previousPhaseProcess(Request $request, Project $project, Process $process): JsonResponse
    {
        return $this->showList([]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function supervisionPhase(Request $request, Project $project, Process $process): JsonResponse
    {
        $this->validate($request, [
            'comments' => 'nullable|string',
        ]);

        $currentProcess = $this->getCurrentProcess($project, $process);
        if (is_bool($currentProcess)) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']', 409);
        }

        $currentDetail = $this->getCurrentProcessDetail($currentProcess);
        if (is_bool($currentDetail)) {
            return $this->errorResponse('El proceso finalizo o aún no se encuentra iniciado', 409);
        }

        // phpcs:ignore
        if (!isset($currentDetail->form_data['rules']['supervisor'])) {
            return $this->errorResponse('Aún no esta iniciado este proceso', 409);
        }

        $countSupervisors = 0;
        // phpcs:ignore
        foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
            if (isset($supervisor['supervision'])) {
                $countSupervisors++;
            }
        }

        // phpcs:ignore
        if (count($currentDetail->form_data['rules']['supervisor']) === $countSupervisors) {
            return $this->errorResponse('Esta fase ya fue revisada por todos sus supervisores', 401);
        }

        $user = User::findOrFail(Auth::id());
        $supervisors = [];
        $continue = false;
        // phpcs:ignore
        foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
            if (($supervisor['user'] && $supervisor['id'] === $user->id) || (!$supervisor['user'] && $supervisor['id'] === $user->role->id)) {
                $supervisor['supervision'] = [
                    'supervision' => true,
                    'datetime' => date('d-m-y h:i:s'),
                    'user' => $user,
                ];
                $continue = true;
            }

            $supervisors[] = $supervisor;
        }
        if ($continue) {
            // phpcs:ignore
            $currentDetail = $this->getCurrentProcessDetail(
                $currentProcess,
                true,
                [
                    // phpcs:ignore
                    'form' => $currentDetail->form_data['form'],
                    'rules' => [
                        'supervisor' => $supervisors,
                        // phpcs:ignore
                        'work_group' => $currentDetail->form_data['rules']['work_group'],
                    ],
                ]
            );
        } else {
            return $this->errorResponse('Este usuario no puede supervisar esta fase', 401);
        }

        return $this->successResponse('supervisado con éxito', 200);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function saveDataFormPhase(Request $request, Project $project, Process $process): JsonResponse
    {
        $this->validate($request, [
            'form' => 'required|array',
            'comments' => 'nullable|string',
        ]);

        $currentProcess = $this->getCurrentProcess($project, $process);
        if (is_bool($currentProcess)) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']', 409);
        }

        $currentDetail = $this->getCurrentProcessDetail($currentProcess);
        if (is_bool($currentDetail)) {
            return $this->errorResponse('El proceso finalizo o aún no se encuentra iniciado', 409);
        }

        // phpcs:ignore
        if (!isset($currentDetail->form_data['rules']['work_group'])) {
            return $this->errorResponse('Aún no esta iniciado este proceso', 409);
        }

        $countWorkGroup = 0;
        // phpcs:ignore
        foreach ($currentDetail->form_data['rules']['work_group'] as $workGroup) {
            if (isset($workGroup['supervision'])) {
                $countWorkGroup++;
            }
        }
        // phpcs:ignore
        if ($countWorkGroup === count($currentDetail->form_data['rules']['work_group'])) {
            return $this->errorResponse('Esta fase ya fue atendida por todo el equipo de trabajo', 401);
        }

        $user = User::findOrFail(Auth::id());
        $workGroups = [];
        $continue = false;
        // phpcs:ignore
        foreach ($currentDetail->form_data['rules']['work_group'] as $workGroup) {
            if (($workGroup['user'] && $workGroup['id'] === $user->id) || (!$workGroup['user'] && $workGroup['id'] === $user->role->id)) {
                $workGroup['supervision'] = [
                    'supervision' => true,
                    'datetime' => date('d-m-y h:i:s'),
                    'user' => $user,
                ];
                $continue = true;
            }

            $workGroups[] = $workGroup;
        }

        if ($continue) {
            // phpcs:ignore
            $currentDetail = $this->getCurrentProcessDetail(
                $currentProcess,
                true,
                [
                    // phpcs:ignore
                    'form' => $request->get('form'),
                    'rules' => [
                        // phpcs:ignore
                        'supervisor' => $currentDetail->form_data['rules']['supervisor'],
                        'work_group' => $workGroups,
                    ],
                ]
            );
        } else {
            return $this->errorResponse('Este usuario no puede contribuir en esta fase', 401);
        }

        return $this->successResponse('Formulario guardado con éxito', 200);
    }

    /**
     * @param Project $project
     * @param Process $process
     *
     * @return mixed
     */
    public function getCurrentProcess(Project $project, Process $process): mixed
    {
        $currentProcess = false;
        foreach ($project->process as $processes) {
            if ($processes['id'] === $process->id) {
                $currentProcess = $processes;
            }
        }

        return $currentProcess;
    }

    /**
     * @param mixed $currentProcess
     * @param bool|null $update
     * @param array|null $formData
     *
     * @return mixed
     */
    public function getCurrentDetailProcessProject(mixed $currentProcess, ?bool $update = false, ?array $formData = []): mixed
    {
        $detailProjectProcess = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)->get();
        $currentDetail = false;
        foreach ($detailProjectProcess as $detail) {
            if ($detail->detailProject->finished === DetailProject::$CURRENT) {
                $currentDetail = $detail->detailProject;
                if ($update) {
                    $detail->detailProject->form_data = $formData;
                    $detail->detailProject->save();
                }
                break;
            }
        }

        return $currentDetail;
    }

    /**
     * @param Project $project
     * @param Process $process
     *
     * @return bool
     */
    public function newDetailPhaseProcess(Project $project, Process $process, string $comments): bool
    {
        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetailProcessProject = $this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process));
        $currentDetailProcessProject->finished = DetailProject::$FINISHED;
        $currentDetailProcessProject->comments = $comments;

        $currentPhaseConfig = [];
        $nextPhase = [];
        foreach ($currentProcess->config['order_phases'] as $config) {
            // phpcs:ignore
            if ($config['phase']['id'] === $currentDetailProcessProject->phases_process_id) {
                $currentPhaseConfig = $config;
            }
        }

        foreach ($currentProcess->config['order_phases'] as $config) {
            //TODO validar si salto o previo de fase para sumar o checar
            if ($config['order'] === ($currentPhaseConfig['order'] + 1)) {
                $nextPhase = $config;
            }
        }
        $currentInvolved = [];
        foreach ($project->config as $projectConfig) {
            if ($projectConfig['process']['id'] === $currentProcess->id) {
                foreach ($projectConfig['phases'] as $phase) {
                    if ($phase['phase']['id'] === $nextPhase['id']) {
                        $currentInvolved = $phase['involved'];
                    }
                }
            }
        }

        if (!empty($nextPhase)) {
            DB::beginTransaction();
            try {
                $currentDetailProcessProject->save();

                $detailProject = new DetailProject();
                $detailProject->comments = 'Phase in progress';
                $detailProject->finished = DetailProject::$CURRENT;
                // phpcs:ignore
                $detailProject->phases_process_id = $nextPhase['phase']['id'];
                // phpcs:ignore
                $detailProject->form_data = ['form' => $nextPhase['phase']['form'], 'rules' => $currentInvolved];
                $detailProject->save();
                $detailProject->processProject()->attach($currentProcess->pivot->id);
                DB::commit();
//                foreach ($project->process as $pProcess) {
//                    $detailProcesses = DetailProjectProcessProject::where('process_project_id', $pProcess->pivot->id)->get();
//                    foreach ($detailProcesses as $dProcess) {
//                        $dProcess->detailProject;
//                    }
//                    $pProcess->detailProcess = $detailProcesses;
//                }
            } catch (QueryException $e) {
                DB::rollBack();

                return false;
            }
        }

        return true;
    }

    /**
     * @param Project $project
     * @param Process $process
     *
     * @return bool|array
     */
    public function validProjectProcess(Project $project, Process $process): bool|array
    {
        $message = false;

        if (is_bool($this->getCurrentProcess($project, $process))) {
            $message = 'El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']';
        }

        if (is_bool($this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process)))) {
            $message = 'the process finish';
        }

        return $message;
    }

}
