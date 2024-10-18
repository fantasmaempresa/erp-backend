<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

class SecondPreventiveNoticeController extends ApiController
{
    public function getStructure(...$args)
    {
        $project = $args[0];
        $reportTextData = json_decode(Storage::get('reports/second_notice/SecondNotice.json'));

        $operations = ReportUtils::getOperationData($project);
        $grantors = ReportUtils::getGrantorData($project);
        $procedureData = [
            $project->procedure->place->name,
            number_format(str_replace('_', '', $project->procedure->folio->book->name), 0, '.', ','),
            number_format(str_replace('_', '', $project->procedure->folio->name), 0, '.', ','),
            $project->procedure->date,
            $project->procedure->value_operation,
            $project->procedure->staff->initials(),
            $project->procedure->name,
        ];

        // PROCEDURE DATA
        $reportTextData->content[1]->text = $reportTextData->content[1]->text . $procedureData[0];
        $reportTextData->content[6]->text = $procedureData[1];
        $reportTextData->content[7]->text = $procedureData[2];
        $reportTextData->content[8]->text = $procedureData[3];
        $reportTextData->content[9]->text = str_replace('_', $procedureData[4], $reportTextData->content[9]->text);

        foreach ($operations as $operation) {
            if(strpos($operation, "APLICACION DE BIENES") !== false) {
                $reportTextData->content[9]->text = "VALOR DE OPERACIÓN DE LA APLICACIÓN: VALOR QUE ARROJE EL AVALÚO CATASTRAL." . " \n \n" . $reportTextData->content[9]->text;
                break;
            }
        }

        $reportTextData->content[14]->text = str_replace('_', $procedureData[5], $reportTextData->content[14]->text);
        $reportTextData->content[15]->text = str_replace('_', $procedureData[6], $reportTextData->content[15]->text);

        // OPERATIONS
        $reportTextData->content[3]->text = str_replace('_', ReportUtils::operationTextReplace($operations), $reportTextData->content[3]->text);

        // GRANTORS
        $reportTextData->content[4]->text = ReportUtils::grantorTextReplace($grantors);

        // DATA CONFIGURATION
        $dataConfig = ReportUtils::configureData($operations, $grantors);

        $dataConfig[] = [
            'title' => 'procedure',
            'sheets' => $procedureData
        ];

        $reportTextData->dataConfig = $dataConfig;

        return $reportTextData;
    }

    public function getDocument()
    {
        return [
            "parameters" => [],
            "jasperPath" => Storage::path('reports/second_notice/SECOND_NOTICE.jasper'),
            "output" => Storage::path('reports/second_notice/SECOND_NOTICE.rtf'),
            "documentType" => "rtf",
        ];
    }
}
