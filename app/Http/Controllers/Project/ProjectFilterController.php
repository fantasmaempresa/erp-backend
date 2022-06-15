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
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectFilterController extends ApiController
{
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

        $response = false;

        // phpcs:ignore
        if (isset($currentDetail->form_data['rules'])) {
            // phpcs:ignore
            foreach ($currentDetail->form_data['rules']['supervisor'] as $supervisor) {
                // phpcs:ignore
                if ($supervisor['user'] && $user->id === $supervisor['id']) {
                    // phpcs:ignore
                    $response = $currentDetail->form_data['form'];
                } elseif ($user->role->id === $supervisor['id']) {
                    // phpcs:ignore
                    $response = $currentDetail->form_data['form'];
                }
            }
        }

        if ($response) {
            return $this->showList($response);
        } else {
            return $this->errorResponse('este usuario no tiene persmisos para ver el formulario actual');
        }
    }

    /**
     * @return JsonResponse
     */
    public function getMyProjects(): JsonResponse
    {

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
}
