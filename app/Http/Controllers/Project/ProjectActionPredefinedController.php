<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Project\PredefinedProjects\DomainTransferController;
use App\Models\Process;
use App\Models\Project;
use Illuminate\Http\Request;

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

        return $dispatcher->getPahsesWithFormat($request->get('namePhase'), $request->get('data'));
    }
}
