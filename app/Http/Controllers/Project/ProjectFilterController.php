<?php

/**
 * CODE
 * ProjectFilter Controller
 */

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ApiController;
use App\Models\DetailProject;
use App\Models\DetailProjectProcessProject;
use App\Models\PhasesProcess;
use App\Models\Process;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectFilterController extends ApiController
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getMyProjects(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        $user = User::findOrFail(Auth::id());
        // phpcs:ignore
        if ($user->role_id == Role::$ADMIN) {
            $projects = Project::where('finished', '<>', Project::$FINISHED)
                ->with('process')
                ->with('processProjectThrough')
                ->paginate($paginate);
        } else {
            $projects = $user->projects()->where('finished', '<>', Project::$FINISHED)->with('process')->with('processProjectThrough')
                ->paginate($paginate);
            if (count($projects) == 0) {
                $projectsAux = Project::where('finished', '<>', Project::$FINISHED)->get();
                $resultProjectsID = [];
                foreach ($projectsAux as $project) {
                    foreach ($project->config as $config) {
                        foreach ($config['phases'] as $phase) {
                            foreach ($phase['involved']['supervisor'] as $supervisor) {
                                if (($supervisor['user'] && $supervisor['id'] == $user->id) ||
                                    (!$supervisor['user'] &&
                                        $supervisor['id'] == $user->role->id)
                                ) {
                                    $resultProjectsID[] = $project->id;
                                    continue (4);
                                }
                            }
                            foreach ($phase['involved']['work_group'] as $work_group) {
                                if (($work_group['user'] && $work_group['id'] == $user->id) ||
                                    (!$work_group['user'] &&
                                        $work_group['id'] == $user->role->id)
                                ) {
                                    $resultProjectsID[] = $project->id;
                                    continue (4);
                                }
                            }
                        }
                    }
                }
                if (count($resultProjectsID) > 0) {
                    $projects = Project::whereIn('id', $resultProjectsID)
                        ->with('process')
                        ->with('processProjectThrough')
                        ->paginate($paginate);
                }
            }
        }
        return $this->showList($projects);
    }

    /**
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function getResumeProject(Project $project, Process $process): JsonResponse
    {
        $currentProcess = $this->getCurrentProcess($project, $process);
        $detailProjectProcess = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)
            ->with('processProject')
            ->with('detailProject')
            ->get();
        foreach ($detailProjectProcess as $detail) {
            $detail->detailProject->phase;
        }

        return $this->showList($detailProjectProcess);
    }

    /**
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function getCurrentPhaseForm(Project $project, Process $process): JsonResponse
    {

        $currentProcess = $this->getCurrentProcess($project, $process);
        if (is_bool($currentProcess)) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']', 409);
        }
        $user = User::findOrFail(Auth::id());
        $currentDetail = $this->getCurrentProcessDetail($currentProcess);

        if (is_bool($currentDetail)) {
            return $this->errorResponse('El proceso finalizo o aún no ha inicado', 409);
        }
        $response = $this->errorResponse('este usuario no tiene permisos para ver el formulario actual', 409);
        // phpcs:ignore
        if (isset($currentDetail->form_data['rules'])) {
            // phpcs:ignore]
            $phaseR['values_form'] = $currentDetail->form_data['values_form'] ?? [];
            $phaseR['form'] = $currentDetail->form_data['form'];
            $phaseR['type_form'] = $currentDetail->form_data['type_form'] ?? PhasesProcess::$TYPE_PHASE_PREDEFINED_FORM;
            $phaseR['controls'] = $this->getControlsCurrentForm($project, $process, $user);
            $phaseR['procedure'] = $project->procedure;
            // phpcs:ignore

            $response = $this->showList($phaseR);
        }
        return $response;
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
     *
     * @return mixed
     */
    public function getCurrentProcessDetail(mixed $currentProcess): mixed
    {
        $detailProjectProcess = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)->get();
        $currentDetail = false;
        foreach ($detailProjectProcess as $detail) {
            if ($detail->detailProject->finished === DetailProject::$CURRENT) {
                $currentDetail = $detail->detailProject;
                break;
            }
        }

        return $currentDetail;
    }

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

    public function getControlsCurrentForm(Project $project, Process $process, User $user): array
    {


        $currentProcess = $this->getCurrentProcess($project, $process);
        $currentDetail = $this->getCurrentProcessDetail($currentProcess);
        $isSupervisor = false;
        $controls = ['next' => false, 'prev' => false, 'supervision' => false, 'saveData' => false, 'completeProcess' => false, 'correction' => false];

        //Verifica que el usuario que solicita el formulario 
        foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
            if ($supervisor['user'] && $user->id == $supervisor['id']) {
                // phpcs:ignore
                if (!isset($supervisor['supervision'])) {
                    $controls['supervision'] = true;
                }
                $isSupervisor = true;
                break;
            }
            if (!$supervisor['user'] && $user->role->id == $supervisor['id']) {
                if (!isset($supervisor['supervision'])) {
                    $controls['supervision'] = true;
                }
                $isSupervisor = true;
                break;
            }
        }

        if ($isSupervisor) {
            //Revisa que el formulario ya tenga datos para que pueda supervisar sino mandar negativo el botón
            //Se activa el boton de guardar información por si quiere actualizar
            if ($currentDetail->form_data['type_form'] == PhasesProcess::$TYPE_PHASE_PREDEFINED_FORM) {
                if (empty($currentDetail->form_data['values_form'])) {
                    $controls['supervision'] = false;
                    $controls['saveData'] = true;
                }
            } else if ($currentDetail->form_data['type_form'] == PhasesProcess::$TYPE_PHASE_CREATE_FORM) {
                foreach ($currentDetail->form_data['form'] as $field) {
                    if (!isset($field['value']) && empty($field['value'])) {
                        $controls['supervision'] = false;
                        $controls['saveData'] = true;
                    }
                }
            }
        } else {
            foreach ($currentDetail->form_data['rules']['work_group'] as $workGroup) {
                // phpcs:ignore
                if (($workGroup['user'] && $user->id === $workGroup['id']) || (!$workGroup['user'] && $user->role->id === $workGroup['id'])) {
                    // phpcs:ignore
                    if (!isset($workGroup['supervision'])) {
                        $controls['supervision'] = true;
                        $controls['saveData'] = false;
                        break;
                    }
                }
            }
        }

        $controls['next'] = $this->checkContinueNextPhase($currentDetail->form_data['rules'], $user, $isSupervisor);
        //TODO: revisar que esta fase tenga un previo antes de activar el botón
        $controls['prev'] = true;

        //Revisa que si exista una siguiente phase, en caso contrario desactiva next y activa completar el proceso

        foreach ($project->config as $config) {
            if ($config['process']['id'] == $process->id) {
                foreach ($config['phases'] as $key => $phase) {
                    if ($currentDetail->phases_process_id == $phase['phase']['id']) {
                        if ($key == (count($config['phases']) - 1)) {
                            $controls['next'] = false;
                            $controls['completeProcess'] = true;
                        }
                    }
                }
            }
        }

        if ($currentDetail->form_data['end_process']) {
            $controls['next'] = false;
            $controls['completeProcess'] = true;
        }

        //TODO todo lo anterior son los controles básicos, aquí vienen los controles de diferentes casos
        //Controles si es supervisor y workgroup
        $controls['correction'] = !$controls['saveData'];

        return $controls;
    }
}
