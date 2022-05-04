<?php
/**
 * CODE
 * ProjectAction Controller
 */

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ApiController;
use App\Models\DetailProject;
use App\Models\DetailProjectProcessProject;
use App\Models\Process;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'comments' => 'required|string',
        ]);

        $project->process;
        $continue = false;
        $currentProcess = [];
        foreach ($project->process as $processes) {
            if ($processes['id'] === $process->id) {
                $currentProcess = $processes;
                $continue = true;
            }
        }
        $detailProcesses = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)->first();

        if (!empty($detailProcesses)) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] del proyecto [' . $project->name . '] ya esta iniciado consulte el formulario en curso', 409);
        }

        if (!$continue) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']', 409);
        }
        $currentPhase = [];
        foreach ($process->config['order_phases'] as $phase) {
            if ($phase['order'] === 1) {
                $currentPhase = $phase;
            }
        }
        $detailProject = new DetailProject();
        $detailProject->comments = $request->get('comments');
        $detailProject->finished = DetailProject::$CURRENT;
        // phpcs:ignore
        $detailProject->phases_process_id = $currentPhase['phase']['id'];
        // phpcs:ignore
        $detailProject->form_data = ['phase_init' => true];
        $detailProject->save();


        $detailProject->processProject()->attach($currentProcess->pivot->id);
        foreach ($project->process as $process) {
            //TODO verificar la relación para acceder a través de los modelos
            $detailProcesses = DetailProjectProcessProject::where('process_project_id', $process->pivot->id)->get();
            foreach ($detailProcesses as $dProcess) {
                $dProcess->detailProject;
            }
            $process->detailProcess = $detailProcesses;
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
        //aqui mandaría la información del formulario anterior para guardarlo y marcarlo como completa y poner la siguiente fase
        //en ejecución.

        $this->validate($request, [
            'form' => 'required|array',
            'comments' => 'required|string',
            'next' => 'required|bool',
        ]);

        $continue = false;
        $currentProcess = [];
        foreach ($project->process as $processes) {
            if ($processes['id'] === $process->id) {
                $currentProcess = $processes;
                $continue = true;
            }
        }

        if (!$continue) {
            // phpcs:ignore
            return $this->errorResponse('El proceso [' . $process->name . '] no se encuenta asiganado a este proyecto [' . $project->name . ']', 409);
        }

        $detailProjectProcess = DetailProjectProcessProject::where('process_project_id', $currentProcess->pivot->id)->get();
        $currentDetail = [];
        foreach ($detailProjectProcess as $detail) {
            if ($detail->detailProject->finished === DetailProject::$CURRENT) {
                $currentDetail = $detail->detailProject;
                break;
            }
        }

        $currentDetail->finished = DetailProject::$FINISHED;
        // phpcs:ignore
        $currentDetail->form_data = $request->get('form');
        $currentDetail->save();

        $currentPhaseConfig = [];
        foreach ($currentProcess->config['order_phases'] as $config) {
            // phpcs:ignore
            if ($config['phase']['id'] === $currentDetail->phases_process_id) {
                $currentPhaseConfig = $config;
            }
        }
        $nextPhase = [];
        $currentPhaseConfig = $request->get('next')
            ? $currentPhaseConfig['next']
            : $currentPhaseConfig['previous'];

        if (empty($currentPhaseConfig)) {
            return $this->errorResponse('this phase empty next', 409);
        }

        $detailProject = new DetailProject();
        $detailProject->comments = $request->get('comments');
        $detailProject->finished = DetailProject::$CURRENT;
        // phpcs:ignore
        $detailProject->phases_process_id = $currentPhaseConfig['phase']['id'];
        // phpcs:ignore
        $detailProject->form_data = ['phase_init' => true];
        $detailProject->save();

        $detailProject->processProject()->attach($currentProcess->pivot->id);

        foreach ($project->process as $process) {
            //TODO verificar la relación para acceder a través de los modelos
            $detailProcesses = DetailProjectProcessProject::where('process_project_id', $process->pivot->id)->get();
            foreach ($detailProcesses as $dProcess) {
                $dProcess->detailProject;
            }
            $process->detailProcess = $detailProcesses;
        }

        return $this->showList($project);
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
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function getCurrentPhaseForm(Project $project, Process $process): JsonResponse
    {

        return $this->showList([]);
    }
}
