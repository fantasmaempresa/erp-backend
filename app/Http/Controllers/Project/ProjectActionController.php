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
use App\Events\NotificationEvent;
use App\Events\ProjectActionsEvent;
use App\Models\Role;

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
                $end_process = $phase['end_process'];
                $phase_previous = $phase['previous'];
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

        $notification = $this->createNotification([
            'title' => 'Proyecto comenzado!',
            'message' => "El proyecto " . $project->name . "se ha iniciado"
        ]);

        $this->sendNotification(
            $notification,
            null,
            new NotificationEvent($notification, 0, Role::$ADMIN, [])
        );

        event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_MY_PROJECT_ACTION, 'startProject'), $project, $process));

        return $this->showList($this->newDetailProject($project, $currentProcess, $currentPhase, $currentInvolved, $end_process, $phase_previous));
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
            $notification = $this->createNotification([
                'title' => 'Proyecto ha sido terminado',
                'message' => "El proyecto " . $project->name . "ha sido terminado"
            ]);

            $this->sendNotification(
                $notification,
                null,
                new NotificationEvent($notification, 0, Role::$ADMIN, [])
            );

            event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_MY_PROJECT_ACTION, 'finishProject'), $project, null));
            event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_CURRENT_FORM_ACTION, 'finishProject'), $project, null));
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

        $continueProcess = $this->validProjectProcess($project, $process);
        if ($continueProcess) {
            // phpcs:ignore
            return $this->errorResponse($continueProcess, 409);
        }

        $currentDetail = $this->getCurrentDetailProcessProject($this->getCurrentProcess($project, $process));
        // phpcs:ignore
        if (!isset($currentDetail->form_data['rules']['supervisor']) && !isset($currentDetail->form_data['rules']['work_group'])) {
            return $this->errorResponse('Aún no esta iniciado este proceso', 409);
        }
        $user = User::findOrFail(Auth::id());

        $isSupervisor = false;
        foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
            if (($supervisor['user'] && $user->id == $supervisor['id']) || (!$supervisor['user'] && $user->role->id == $supervisor['id'])) {
                $isSupervisor = true;
                break;
            }
        }

        if ($request->get('prev')) {
            if (empty($currentDetail->form_data['previous']['phase'])) {
                return $this->errorResponse('No existe una fase siguiente', 409);
            }
        } else {
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
            if (!$this->checkContinueNextPhase($currentDetail->form_data['rules'], $user, $isSupervisor)) {
                return $this->errorResponse('Aún no se puede pasar a la siguiente fase', 409);
            }
        }

        $response = $this->newDetailProjectProcess($project, $process, $request->get('comment'), $request->get('prev'));
        if ($response) {
            $response = $this->showList($response);
            $notification = $this->createNotification([
                'title' => 'Avance de proyecto',
                'message' => 'Proyecto ' . $project->name . ' avanzo de fase en el proceso ' . $process->name
            ]);

            $this->sendNotification(
                $notification,
                null,
                new NotificationEvent($notification, 0, 0, [])
            );

            event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_CURRENT_FORM_ACTION, 'skipPhase'), $project, $process));
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
                    'end_process' => $currentDetail->form_data['end_process'],
                ]
            );
        } else {
            return $this->errorResponse('Este usuario no puede supervisar esta fase', 409);
        }

        $notification = $this->createNotification([
            'title' => 'Información Supervisada',
            'message' => 'Proyecto ' . $project->name . ' supervision de fase en el proceso ' . $process->name
        ]);

        $this->sendNotification(
            $notification,
            null,
            new NotificationEvent($notification, 0, 0, [])
        );

        event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_CURRENT_FORM_ACTION, 'skipPhase'), $project, $process));

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

        $isSupervisor = false;
        foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
            if (($supervisor['user'] && $user->id == $supervisor['id']) || (!$supervisor['user'] && $user->role->id == $supervisor['id'])) {
                $isSupervisor = true;
                break;
            }
        }
        
        $workGroups = $this->checkContinueNextPhase($currentDetail->form_data['rules'], $user);
        $supervisors = $this->checkContinueNextPhase($currentDetail->form_data['rules'], $user, $isSupervisor);
        if (!$workGroups && !$supervisors) {
            return $this->errorResponse('Aún no participan todos los involucrados', 409);
        }

        $notification = $this->createNotification([
            'title' => 'Proyecto culminado',
            'message' => 'Proyecto ' . $project->name . ' termino el procedimiento ' . $process->name
        ]);

        $this->sendNotification(
            $notification,
            null,
            new NotificationEvent($notification, 0, 0, [])
        );

        event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_CURRENT_FORM_ACTION, 'skipPhase'), $project, $process));

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
            'correction' => 'required|boolean',
        ]);

        if ($this->validProjectProcess($project, $process)) {
            // phpcs:ignore
            return $this->errorResponse($this->validProjectProcess($project, $process), 409);
        }

        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetail = $this->getCurrentDetailProcessProject($currentProcess);
        $user = User::findOrFail(Auth::id());

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
        $workGroups = $this->checkSupervisionPhaseProcess($currentDetail->form_data['rules']['work_group'], $user, $request->get('correction'), true);
        $supervisor = $this->checkSupervisionPhaseProcess($currentDetail->form_data['rules']['supervisor'], $user, $request->get('correction'), true);

        if ($workGroups || $supervisor) {
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
                        'supervisor' => $supervisor ?? $currentDetail->form_data['rules']['supervisor'],
                        'work_group' => $workGroups ?? $currentDetail->form_data['rules']['work_group'],
                    ],
                    'end_process' => $currentDetail->form_data['end_process'],
                ]
            );
        } else {
            return $this->errorResponse('Este usuario no puede contribuir en esta fase', 409);
        }

        event(new ProjectActionsEvent(Project::getActionSystem(Project::$RELOAD_CURRENT_FORM_ACTION, 'saveFormData'), $project, $process));
        return $this->successResponse('Formulario guardado con éxito', 200);
    }

    /**
     * @param $rules
     * @param User $user
     * @param bool $isSupervior
     *
     * @return bool
     */
    public function checkContinueNextPhase($rules, User $user, bool $isSupervisor = false): bool
    {
        $continue = true;
        foreach ($rules['work_group'] as $stake) {
            if ($stake['mandatory_work'] && !isset($stake['contribution'])) {
                $continue = false;
                break (1);
            }
        }

        foreach ($rules['supervisor'] as $stake) {
            if ($stake['mandatory_supervision'] && !isset($stake['supervision'])) {
                $continue = false;
                break (1);
            }
        }

        $permisionUser = false;
        $checkUser = $isSupervisor ? $rules['supervisor'] : $rules['work_group'];
        foreach ($checkUser as $stake) {
            if (($stake['user'] && $stake['id'] === $user->id) || (!$stake['user'] && $stake['id'] === $user->role->id)) {
                $permisionUser = true;
            }
        }

        return $continue && $permisionUser;
    }
    /**
     * @param array $rules
     * @param User $user
     * @param bool $correction
     * @param bool $workgruop
     *
     * @return bool|array
     */
    public function checkSupervisionPhaseProcess(array $rules, User $user, bool $correction = false, bool $workgruop = false): null|array
    {
        if (!$correction) {
            $countSupervision = 0;
            foreach ($rules as $supervision) {
                if (isset($supervision['supervision'])) {
                    $countSupervision++;
                }
            }

            if (count($rules) === $countSupervision) {
                return null;
            }
        }

        $supervisors['continue'] = false;
        foreach ($rules as $supervisor) {
            if (($supervisor['user'] && $supervisor['id'] === $user->id) || (!$supervisor['user'] && $supervisor['id'] === $user->role->id)) {
                if ($workgruop) {
                    $supervisor['contribution'] = [
                        'contribution' => true,
                        'datetime' => date('d-m-y h:i:s'),
                        'user' => $user,
                    ];
                } else {
                    $supervisor['supervision'] = [
                        'supervision' => true,
                        'datetime' => date('d-m-y h:i:s'),
                        'user' => $user,
                    ];
                }
                $supervisors['continue'] = true;
            }
            $supervisors[] = $supervisor;
        }

        if ($supervisors['continue']) {
            unset($supervisors['continue']);

            return $supervisors;
        }

        return null;
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

        if (($prev && ($currentPhaseConfig['order'] === 1)) || (!$prev && ($currentPhaseConfig['order'] === $lastOrder))) {
            return false;
        }

        foreach ($currentProcess->config['order_phases'] as $config) {
            if ($prev && ($config['order'] === $currentPhaseConfig['order'])) {
                $nextPhase = PhasesProcess::findOrFail($config['previous']['phase']['id']);
                break(1);
            } 
            if ($config['order'] === ($currentPhaseConfig['order'] + 1)) {
                $nextPhase = PhasesProcess::findOrFail($config['phase']['id']);
                break(1);
            }
        }
        $currentInvolved = [];
        $end_process = false;
        $previous = [];
        foreach ($project->config as $projectConfig) {
            if ($projectConfig['process']['id'] === $currentProcess->id) {
                foreach ($projectConfig['phases'] as $phase) {
                    if ($phase['phase']['id'] === $nextPhase->id) {
                        $currentInvolved = $phase['involved'];
                        foreach ($currentProcess->config['order_phases'] as $order_phases){
                            if ($order_phases['phase']['id'] === $nextPhase->id) {
                                $end_process = $order_phases['end_process'];
                                $previous = $order_phases['previous'];
                            }
                        }
                    }
                }
            }
        }

        if (!empty($nextPhase)) {
            DB::beginTransaction();
            try {
                $currentDetailProcessProject->save();
                $project = $this->newDetailProject(
                    $project,
                    $currentProcess,
                    $nextPhase,
                    $currentInvolved,
                    $end_process,
                    $previous,
                );
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
    public function newDetailProject(
        Project $project,
        mixed $currentProcess,
        PhasesProcess $phasesProcess,
        array $currentInvolved,
        bool $endPhaseProcess,
        array $prev,
    ): Project {
        $detailProject = new DetailProject();
        $detailProject->comments = 'Phase in progress';
        $detailProject->finished = DetailProject::$CURRENT;
        // phpcs:ignore
        $detailProject->phases_process_id = $phasesProcess->id;
        // phpcs:ignore
        $formFormart = null;
        if(!empty($phasesProcess->withFormat)){

            $formFormart = $phasesProcess->form;
            $formFormart['formats'] = $phasesProcess->withFormat;
        }

        $detailProject->form_data = [
            'form' => $formFormart ?? $phasesProcess->form,
            'rules' => $currentInvolved,
            'type_form' =>  $phasesProcess->type_form,
            'values_form' => [],
            'end_process' => $endPhaseProcess,
            'previous' => $prev,
        ];
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
