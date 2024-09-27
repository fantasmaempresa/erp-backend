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
use App\Models\ProjectQuote;
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
                $currentPhase = PhasesProcess::findOrFail($phase['phase']['id']);
            }
        }

        $currentInvolved = [];
        foreach ($project->config as $config) {
            if ($config['process']['id'] === $currentProcess->id) {
                foreach ($config['phases'] as $phase) {
                    if ($phase['phase']['id'] === $currentPhase->id) {
                        $currentInvolved = $phase['involved'];
                    }
                }
            }
        }


        //        $process->finished = Project::$INPROGRESS;
        $project->process()->updateExistingPivot($process->id, ['status' => Process::$START]);
        $process->save();

        return $this->showList($this->newDetailProject($project, $currentProcess, $currentPhase, $currentInvolved));
    }

    /**
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function finishProject(Project $project): JsonResponse
    {
        DB::beginTransaction();

        try {
            $project->finished = Project::$FINISHED;
            $project->save();
            DB::commit();
        } catch (QueryException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 409);
        }


        return $this->successResponse('Project finished');
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
            'prev' => 'required|bool',
        ]);

        if ($this->validProjectProcess($project, $process)) {
            // phpcs:ignore
            return $this->errorResponse($this->validProjectProcess($project, $process), 409);
        }

        $currentDetail = $this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process));
        // phpcs:ignore
        if (!isset($currentDetail->form_data['rules']['supervisor']) && !isset($currentDetail->form_data['rules']['work_group'])) {
            return $this->errorResponse('Aún no esta iniciado este proceso', 409);
        }
        $user = User::findOrFail(Auth::id());
        // phpcs:ignore
        $workGroups = $this->checkContinueNextPhase($currentDetail->form_data['rules']['work_group'], $user);
        // phpcs:ignore
        $supervisors = $this->checkContinueNextPhase($currentDetail->form_data['rules']['supervisor'], $user);

        //Revisa que si exista una siguiente phase
        foreach ($project->config as $config) {
            if ($config['process']['id'] == $process->id) {
                foreach ($config['phases'] as $key => $phase) {
                    if ($currentDetail->phases_process_id == $phase['phase']['id']) {
                        if ($key == (count($config['phases']) - 1)) {
                            return $this->errorResponse('No existe una fase siguiente', 409);
                        }
                    }
                }
            }
        }


        if (!$workGroups && !$supervisors) {
            return $this->errorResponse('Aún no se puede pasar a la siguiente fase', 409);
        }

        $response = $this->newDetailProjectProcess($project, $process, $request->get('comment'), $request->get('prev'));
        if ($response) {
            $response = $this->showList($response);
        } else {
            $response = $this->errorResponse('No existe una previa o siguiente fase', 409);
        }

        return $response;
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

        if ($this->validProjectProcess($project, $process)) {
            // phpcs:ignore
            return $this->errorResponse($this->validProjectProcess($project, $process), 409);
        }

        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetail = $this->getCurrentDetailProcessProject($currentProcess);
        // phpcs:ignore
        if (!isset($currentDetail->form_data['rules']['supervisor'])) {
            return $this->errorResponse('Aún no esta iniciado este proceso', 409);
        }

        $user = User::findOrFail(Auth::id());
        // phpcs:ignore
        $supervisors = $this->checkSupervisionPhaseProcess($currentDetail->form_data['rules']['supervisor'], $user);
        if ($supervisors) {
            // phpcs:ignore
            $currentDetail = $this->getCurrentDetailProcessProject(
                $currentProcess,
                true,
                [
                    // phpcs:ignore
                    'type_form' => $currentDetail->form_data['type_form'],
                    'form' => $currentDetail->form_data['form'],
                    'values_form' => $currentDetail->form_data['values_form'] ?? [],
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
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function completeProcessProject(Project $project, Process $process): JsonResponse
    {
        if ($this->validProjectProcess($project, $process)) {
            // phpcs:ignore
            return $this->errorResponse($this->validProjectProcess($project, $process), 409);
        }

        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetail = $this->getCurrentDetailProcessProject($currentProcess);
        $user = User::findOrFail(Auth::id());

        // phpcs:ignore
        $workGroups = $this->checkContinueNextPhase($currentDetail->form_data['rules']['work_group'], $user);
        // phpcs:ignore
        $supervisors = $this->checkContinueNextPhase($currentDetail->form_data['rules']['supervisor'], $user);

        if (!$workGroups && !$supervisors) {
            return $this->errorResponse('Aún no participan todos los involucrados', 409);
        }

        $currentDetailProcessProject = $this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process));
        $currentDetailProcessProject->finished = DetailProject::$FINISHED;
        $currentDetailProcessProject->comments = 'phase finished process';
        $currentDetailProcessProject->save();
        $project->process()->updateExistingPivot($process->id, ['status' => Process::$FINISHED]);
        $process->save();

        return $this->showMessage('termino proceso exitosamente');
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
    public function saveDataFormPhase(Request $request, Project $project, Process $process): JsonResponse
    {
        $this->validate($request, [
            'form' => 'required|array',
        ]);

        if ($this->validProjectProcess($project, $process)) {
            // phpcs:ignore
            return $this->errorResponse($this->validProjectProcess($project, $process), 409);
        }

        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetail = $this->getCurrentDetailProcessProject($currentProcess);
        $user = User::findOrFail(Auth::id());

        // return $this->showList([$currentDetail, $request->get('form')], 500);
        // phpcs:ignore
        $values_form = [];
        if (!empty($currentDetail->form_data['type_form']) && $currentDetail->form_data['type_form'] == PhasesProcess::$TYPE_PHASE_PREDEFINED_FORM) {
            $form = $currentDetail->form_data['form'];
            $values_form = $request->get('form');

        } elseif (empty($currentDetail->form_data['type_form']) || $currentDetail->form_data['type_form'] == PhasesProcess::$TYPE_PHASE_CREATE_FORM) {
            if (count($request->get('form')) != count($currentDetail->form_data['form'])) {
                return $this->errorResponse('la cantidad de campos no concide', 409);
            }

            $countEqualsFields = 0;
            // phpcs:ignore
            $form = $currentDetail->form_data['form'];
            foreach ($request->get('form') as $key => $value) {
                // phpcs:ignore
                foreach ($currentDetail->form_data['form'] as $_key => $field) {
                    if ($field['key'] == $key) {
                        $form[$_key]['value'] = $value;
                        $countEqualsFields++;
                        continue (2);
                    }
                }
            }
    
            // phpcs:ignore
            if ($countEqualsFields != count($currentDetail->form_data['form'])) {
                return $this->errorResponse('falta un campo o un dato del formulario', 409);
            }
        }

        // phpcs:ignore
        $workGroups = $this->checkSupervisionPhaseProcess($currentDetail->form_data['rules']['work_group'], $user);
        if ($workGroups) {
            // phpcs:ignore
            $currentDetail = $this->getCurrentDetailProcessProject(
                $currentProcess,
                true,
                [
                    // phpcs:ignore
                    'type_form' => $currentDetail->form_data['type_form'] ?? PhasesProcess::$TYPE_PHASE_CREATE_FORM,
                    'form' => $form,
                    'values_form' => $currentDetail->form_data['type_form'] == PhasesProcess::$TYPE_PHASE_CREATE_FORM ?  $form : $values_form,
                    'rules' => [
                        // phpcs:ignore
                        'supervisor' => $currentDetail->form_data['rules']['supervisor'],
                        'work_group' => $workGroups,
                    ],
                ]
            );
        } else {
            return $this->errorResponse('Este usuario no puede contribuir en esta fase', 409);
        }

        return $this->successResponse('Formulario guardado con éxito', 200);
    }

    /**
     * @param $rules
     * @param User $user
     *
     * @return bool
     */
    public function checkContinueNextPhase($rules, User $user): bool
    {
        $countSupervision = 0;
        foreach ($rules as $supervision) {
            if (isset($supervision['supervision'])) {
                $countSupervision++;
            }
        }

        if (count($rules) !== $countSupervision) {
            return false;
        }

        $continue = false;
        foreach ($rules as $supervisor) {
            if (($supervisor['user'] && $supervisor['id'] === $user->id) || (!$supervisor['user'] && $supervisor['id'] === $user->role->id)) {
                $continue = true;
                break;
            }
        }

        return $continue;
    }

    /**
     * @param array $rules
     * @param User $user
     *
     * @return bool|array
     */
    public function checkSupervisionPhaseProcess(array $rules, User $user): bool|array
    {
        $countSupervision = 0;
        foreach ($rules as $supervision) {
            if (isset($supervision['supervision'])) {
                $countSupervision++;
            }
        }

        if (count($rules) === $countSupervision) {
            return false;
        }

        $supervisors['continue'] = false;
        foreach ($rules as $supervisor) {
            if (($supervisor['user'] && $supervisor['id'] === $user->id) || (!$supervisor['user'] && $supervisor['id'] === $user->role->id)) {
                $supervisor['supervision'] = [
                    'supervision' => true,
                    'datetime' => date('d-m-y h:i:s'),
                    'user' => $user,
                ];
                $supervisors['continue'] = true;
            }
            $supervisors[] = $supervisor;
        }

        if ($supervisors['continue']) {
            unset($supervisors['continue']);

            return $supervisors;
        }

        return false;
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
     * @param string $comments
     * @param bool|null $prev
     *
     * @return bool|Project
     */
    public function newDetailProjectProcess(Project $project, Process $process, string $comments, ?bool $prev = false): bool|Project
    {
        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetailProcessProject = $this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process));
        $currentDetailProcessProject->finished = DetailProject::$FINISHED;
        $currentDetailProcessProject->comments = $comments;

        $currentPhaseConfig = [];
        $nextPhase = [];
        $lastOrder = 0;
        foreach ($currentProcess->config['order_phases'] as $config) {
            // phpcs:ignore
            if ($config['phase']['id'] === $currentDetailProcessProject->phases_process_id) {
                $currentPhaseConfig = $config;
            }
            if ($lastOrder < $config['order']) {
                $lastOrder = $config['order'];
            }
        }

        if (($prev && $currentPhaseConfig['order'] === 1) || (!$prev && $currentPhaseConfig['order'] === $lastOrder)) {
            return false;
        }

        foreach ($currentProcess->config['order_phases'] as $config) {
            if ($prev && $config['order'] === $currentPhaseConfig['order']) {
                $nextPhase = PhasesProcess::findOrFail($config['previous']['phase']['id']);
            } elseif ($config['order'] === ($currentPhaseConfig['order'] + 1)) {
                $nextPhase = PhasesProcess::findOrFail($config['phase']['id']);
            }
        }

        $currentInvolved = [];
        foreach ($project->config as $projectConfig) {
            if ($projectConfig['process']['id'] === $currentProcess->id) {
                foreach ($projectConfig['phases'] as $phase) {
                    if ($phase['phase']['id'] === $nextPhase->id) {
                        $currentInvolved = $phase['involved'];
                    }
                }
            }
        }

        if (!empty($nextPhase)) {
            DB::beginTransaction();
            try {
                $currentDetailProcessProject->save();
                $project = $this->newDetailProject($project, $currentProcess, $nextPhase, $currentInvolved);
                DB::commit();
            } catch (QueryException $e) {
                DB::rollBack();

                return false;
            }
        }

        return $project;
    }

    /**
     * @param Project $project
     * @param mixed $currentProcess
     * @param PhasesProcess $phasesProcess
     * @param array $currentInvolved
     *
     * @return Project
     */
    public function newDetailProject(Project $project, mixed $currentProcess, PhasesProcess $phasesProcess, array $currentInvolved): Project
    {
        $detailProject = new DetailProject();
        $detailProject->comments = 'Phase in progress';
        $detailProject->finished = DetailProject::$CURRENT;
        // phpcs:ignore
        $detailProject->phases_process_id = $phasesProcess->id;
        // phpcs:ignore
        $detailProject->form_data = ['form' => $phasesProcess->form, 'rules' => $currentInvolved, 'type_form' =>  $phasesProcess->type_form, 'values_form' => []];
        $detailProject->save();
        $detailProject->processProject()->attach($currentProcess->pivot->id);
        foreach ($project->process as $pProcess) {
            $detailProcesses = DetailProjectProcessProject::where('process_project_id', $pProcess->pivot->id)->get();
            foreach ($detailProcesses as $dProcess) {
                $dProcess->detailProject;
            }
            $pProcess->detailProcess = $detailProcesses;
        }

        return $project;
    }

    /**
     * @param Project $project
     * @param Process $process
     *
     *
     * @return bool|string
     */
    public function validProjectProcess(Project $project, Process $process): bool|string
    {
        $message = false;

        if (is_bool($this->getCurrentProcess($project, $process))) {
            $message = 'El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']';
        }

        if (is_bool($this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process)))) {
            $message = 'the process finish or not init';
        }

        return $message;
    }

    /**
     * @param Project $project
     * @param ProjectQuote $projectQuote
     *
     * @return JsonResponses
     */
    public function assignQuoteProject(Project $project, ProjectQuote $projectQuote): JsonResponse
    {
        if (!empty($project->project_quote_id)) {

            return $this->errorResponse('this project already have quote', 409);
        }

        $project->project_quote_id = $projectQuote->id;
        $project->save();

        return $this->showOne($project);
    }
}
