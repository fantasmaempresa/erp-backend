<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Project\PredefinedProjects\DomainTransferController;
use App\Models\Process;
use App\Models\Project;
use App\Models\ReportConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Open2code\Pdf\jasper\Report;

class ProjectActionPredefinedController extends ApiController
{
    public $PROCESS_PREDEFINED = [
        'DomainTransfer' => DomainTransferController::class,
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

        $this->validate($request, $dispatcher->getValidatorRequestPhase('namePhase'));

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
            'data' => 'required|array',
        ]);

        if (!isset($this->PROCESS_PREDEFINED[$request->get('nameProcess')])) {
            return $this->errorResponse('Processo no encontrado', 404);
        }

        $classDispacher = $this->PROCESS_PREDEFINED[$request->get('nameProcess')];
        $dispatcher = new $classDispacher;

        if (empty($dispatcher->getPahsesWithFormat($request->get('namePhase')))) {
            return $this->errorResponse('Esta fase no genera documentos', 404);
        }

        // $dispatcher->getPahsesWithFormat($request->get('namePhase'));
        return $dispatcher->executePhase($request->get('namePhase'), $project, $process, $request->get('data'));
        // return $dispatcher->getPahsesWithFormat($request->get('namePhase'), $request->get('data')); 
    }

    public function getReportFormat(Request $request)
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
        ]);
        
        //CHECK IF ALREADY EXISTS
        $reportConfiguration = ReportConfiguration::where('project_id', $project->id)
            ->where('process_id', $process->id)
            ->where('name_process', $request->get('nameProcess'))
            ->where('name_phase', $request->get('namePhase'))
            ->first();

        if (is_null($reportConfiguration)) {
            $reportConfiguration = new ReportConfiguration();
            $reportConfiguration->data = $request->get('data');
            $reportConfiguration->name_process = $request->get('nameProcess');
            $reportConfiguration->name_phase = $request->get('namePhase');
            $reportConfiguration->project_id = $project->id;
            $reportConfiguration->process_id = $process->id;
        } else {
            $reportConfiguration->data = $request->get('data');
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
}
