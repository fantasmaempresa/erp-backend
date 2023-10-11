<?php
/**
 * CODE
 * ProjectFilter Controller
 */

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ApiController;
use App\Models\DetailProject;
use App\Models\DetailProjectProcessProject;
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
                                    continue(4);
                                }
                            }

                            foreach ($phase['involved']['work_group'] as $work_group) {
                                if (($work_group['user'] && $work_group['id'] == $user->id) ||
                                    (!$work_group['user'] &&
                                        $work_group['id'] == $user->role->id)
                                ) {
                                    $resultProjectsID[] = $project->id;
                                    continue(4);
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
        $detailProjectProcess = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)->get();


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
            $phaseR['form'] = $currentDetail->form_data['form'];
            $phaseR['controls'] = ['next' => false, 'prev' => false, 'supervision' => false, 'saveData' => false];

            // phpcs:ignore
            foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
                // phpcs:ignore
                if (($supervisor['user'] && $user->id === $supervisor['id']) ||
                    // phpcs:ignore
                    (!$supervisor['user'] && $user->role->id === $supervisor['id'])) {
                    // phpcs:ignore
                    $phaseR['form'] = $currentDetail->form_data['form'];

                    foreach ($phaseR['form'] as $field) {
                        if($field){
                            continue;
                        }
                    }

                    if (isset($supervisor['supervision']) && $supervisor['supervision']['supervision']) {
                        $phaseR['controls']['supervision'] = false;
                    } else {
                        $phaseR['controls'] = ['next' => false, 'prev' => false, 'supervision' => true];
                    }
                    break;
                }
            }

            // phpcs:ignore
            foreach ($currentDetail->form_data['rules']['work_group'] as $supervisor) {
                // phpcs:ignore
                if (($supervisor['user'] && $user->id === $supervisor['id']) ||
                    // phpcs:ignore
                    (!$supervisor['user'] && $user->role->id === $supervisor['id'])) {
                    // phpcs:ignore
                    $phaseR['form'] = $currentDetail->form_data['form'];
                    if (isset($supervisor['supervision']) && $supervisor['supervision']['supervision']) {
                        $phaseR['controls']['supervision'] = false;
                    } else {
                        $phaseR['controls'] = ['next' => false, 'prev' => false, 'supervision' => false];
                    }
                    break;
                }
            }


            // phpcs:ignore
            $workGroups = $this->checkContinueNextPhase($currentDetail->form_data['rules']['work_group'], $user);
            // phpcs:ignore
            $supervisors = $this->checkContinueNextPhase($currentDetail->form_data['rules']['supervisor'], $user);
            if (!$workGroups && !$supervisors) {
                $phaseR['controls']['next'] = true;
            }
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
}
