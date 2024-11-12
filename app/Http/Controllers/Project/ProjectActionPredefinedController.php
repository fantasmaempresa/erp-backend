<?php

namespace App\Http\Controllers\Project;

use App\Events\ProjectActionsEvent;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Project\PredefinedProjects\DomainTransferController;
use App\Http\Controllers\Project\PredefinedProjects\FormatsProcessController;

use App\Models\Process;
use App\Models\Project;
use App\Models\ReportConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Open2code\Pdf\jasper\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class ProjectActionPredefinedController extends ApiController
{
    public $PROCESS_PREDEFINED = [
        'DomainTransfer' => DomainTransferController::class,
        'FormatsProcess' => FormatsProcessController::class,
    ];

    public function executePhase(Project $project, Process $process, Request $request)
    {
        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'required|array',
        ]);

        if (!isset($this->PROCESS_PREDEFINED[$request->get('nameProcess')])) {
            return $this->errorResponse('Processo no encontrado', 404);
        }

        $classDispacher = $this->PROCESS_PREDEFINED[$request->get('nameProcess')];
        $dispatcher = new $classDispacher;

        if (empty($dispatcher->getPhases($request->get('namePhase')))) {
            return $this->errorResponse('Fase no encontrada', 404);
        }

        //TODO Validar si la fase se puede ejecutar. quizás sea mejor cambiar el orden de lanzamiento de las peticiones desde el front
        return $dispatcher->executePhase(
            $request->get('namePhase'),
            [
                'project' => $project,
                'process' => $process,
                'data' => $request->get('data')
            ]
        );
    }

    public function getStructureFormat(Project $project, Process $process, Request $request)
    {
        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'nullable|array',
            'data.reload' => 'required|boolean'

        ]);

        if (!isset($this->PROCESS_PREDEFINED[$request->get('nameProcess')])) {
            return $this->errorResponse('Processo no encontrado', 404);
        }

        $classDispacher = $this->PROCESS_PREDEFINED[$request->get('nameProcess')];
        $dispatcher = new $classDispacher;

        if (empty($dispatcher->getPahsesWithFormat($request->get('namePhase')))) {
            return $this->errorResponse('Esta fase no genera documentos', 404);
        }

        if ($request->all()['data']['reload']) {
            $this->validate($request, $dispatcher->getValidatorRequestPhase($request->get('namePhase')));
            $originalStructure = $dispatcher->executePhase($request->get('namePhase'), $project, $process, $request->get('data'));
        } else {
            $reports = ReportConfiguration::where([
                ['name_process', '=', $request->get('nameProcess')],
                ['name_phase', '=', $request->get('namePhase')],
                ['project_id', '=', $project->id],
                ['process_id', '=', $process->id],
            ])->orderby('id', 'asc')->get();

            $originalStructure = [];
            if ($reports->count() > 0) {
                $originalStructure = $dispatcher->executePhase($request->get('namePhase'), $project, $process, $request->get('data'));
                foreach ($reports as $report) {
                    $originalStructure->content =  isset($report->data['content']) ? $report->data['content'] : $originalStructure->content;
                    $originalStructure->id_report =  $report->id;
                    if (isset($report->data['lasted_related_report_id'])) {
                        $originalStructure->lasted_related_report_id = ReportConfiguration::findOrFail($report->data['lasted_related_report_id']);
                    }
                }
            } else {
                $this->validate($request, $dispatcher->getValidatorRequestPhase($request->get('namePhase')));
                $originalStructure = $dispatcher->executePhase($request->get('namePhase'), $project, $process, $request->get('data'));
            }
        }
        return $originalStructure;
    }

    public function getReportFormat(Request $request)
    {
        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'nullable|array',
        ]);

        if (!isset($this->PROCESS_PREDEFINED[$request->get('nameProcess')])) {
            return $this->errorResponse('Processo no encontrado', 404);
        }

        $classDispacher = $this->PROCESS_PREDEFINED[$request->get('nameProcess')];
        $dispatcher = new $classDispacher;

        if (empty($dispatcher->getPahsesWithFormatReport($request->get('namePhase')))) {
            return $this->errorResponse('Esta fase no genera documentos', 404);
        }

        $reportParams = $dispatcher->executePhase($request->get('namePhase'), $request->get('data'));


        $jsonData = json_encode($reportParams['data']);
        Storage::put("reports/tempJson.json", $jsonData);

        $report = new Report(
            Storage::path('reports/tempJson.json'),
            $reportParams['parameters'],
            $reportParams['jasperPath'],
            $reportParams['output'],
            $reportParams['documentType'],
        );

        $result = $report->generateReport();
        Storage::delete("reports/tempJson.json");

        return ($result['success'] || $reportParams['documentType'] == "rtf") ? $this->downloadFile($reportParams['output']) : $this->errorResponse($result['message'], 500);
    }

    public function saveFormat(Project $project, Process $process, Request $request)
    {
        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'required|array',
            'data.content' => 'required|array',
            'data.reportConfiguration_id' => 'nullable|int|exists:report_configurations,id',
            'data.lasted_related_report_id' => 'nullable|int|exists:report_configurations,id'
        ]);

        $reportConfiguration = empty($request->all()['data']['reportConfiguration_id'])
            ? null
            : ReportConfiguration::findOrFail($request->all()['data']['reportConfiguration_id']);

        if (is_null($reportConfiguration)) {
            $reportConfiguration = new ReportConfiguration();
            $reportConfiguration->data = $request->all()['data'];
            $reportConfiguration->name_process = $request->get('nameProcess');
            $reportConfiguration->name_phase = $request->get('namePhase');
            $reportConfiguration->project_id = $project->id;
            $reportConfiguration->process_id = $process->id;
        } else {
            $reportConfiguration->data = $request->all()['data'];
        }

        $reportConfiguration->save();

        return $this->showOne($reportConfiguration);
    }

    public function getInfoProject(Project $project, Process $process, Request $request)
    {
        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'nullable|array',
        ]);

        // TODO si es necesario se manda el proceso y la phase por si se quiere personalizar la respuesta al front
        $project->procedure;

        return $this->showList([
            'project' => $project,
            'process' => $process,
        ]);
    }

    public function getLastedRelatedReportsFromReport(Project $project, Process $process, Request $request)
    {

        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        $useLastReport = [
            'generateSecondPreventiveNotice' => ['generateFirstPreventiveNotice'],
            'generateBuySell' => ['generateFirstPreventiveNotice', 'generateSecondPreventiveNotice'],
        ];

        $reports = ReportConfiguration::where('project_id', $project->id)
            ->where('process_id', $process->id)
            ->where('name_process', $request->get('nameProcess'));

        if (isset($useLastReport[$request->get('namePhase')])) {
            $reports = $reports->whereIn('name_phase', $useLastReport[$request->get('namePhase')]);
        } else {
            $reports = $reports->where('name_phase', $request->get('namePhase'));
        }

        $reports = $reports->orderBy('id', 'desc')->get();

        $rReports = $reports->map(function ($report) {
            $report->name = $report->data['title'] ?? $report->name_phase;
            return $report;
        });


        $currentPage = Paginator::resolveCurrentPage('page');
        $paginatedReport = new LengthAwarePaginator(
            $rReports->forPage($currentPage, $paginate),
            $rReports->count(),
            $paginate,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return $this->showList($paginatedReport);
    }

    public function getLastedReportsFromProjects(Project $project, Process $process, Request $request)
    {
        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $reports = ReportConfiguration::where('project_id', $project->id)
            ->where('process_id', $process->id)
            ->where('name_process', $request->get('nameProcess'))
            ->where('name_phase', $request->get('namePhase'))
            ->orderBy('id', 'desc')->get();


        $rReports = $reports->map(function ($report) {
            //TODO: agregar un diccionario de nombres para que se haga la sustitución
            $report->name = $report->data['title'] ?? $report->name_phase;
            return $report;
        });

        $currentPage = Paginator::resolveCurrentPage('page');
        $paginatedReport = new LengthAwarePaginator(
            $rReports->forPage($currentPage, 100),
            $rReports->count(),
            100,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return $this->showList($paginatedReport);
    }

    public function issueEventToPhase(Project $project, Process $process, Request $request)
    {

        $this->validate($request, [
            'nameProcess' => 'required|string',
            'namePhase' => 'required|string',
            'data' => 'required|array',
            'data.message' => 'required|string',
        ]);

        event(
            new ProjectActionsEvent(
                Project::getActionSystem(Project::$SEND_NOTIFY_MY_PROJECT_ACTION, 'show_message'),
                $project,
                $process,
                [
                    'nameProcess' => $request->get('nameProcess'),
                    'namePhase' => $request->get('namePhase'),
                    'message' => $request->all()['data']['message'],
                ]
            )
        );
    }
}
