<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeedsController extends Controller
{
    public function getStructure(...$args)
    {
        $project = $args[0];
        $reportTextData = json_decode(Storage::get('reports/deeds/Deeds.json'));

        $folio = $project->procedure->folio;
        $volume = is_null($folio) ? '' : '(' . number_format($folio->book->name, 0, '.', ',') . ') ' . strtoupper(ReportUtils::numberSpanish($folio->book->name));
        $instrument = is_null($folio) ? '' : '(' . number_format($folio->name, 0, '.', ',') . ') ' . strtoupper(ReportUtils::numberSpanish($folio->name));

        $procedureData = [
            $volume,
            $instrument,
            $project->procedure->date,
        ];

        $operations = ReportUtils::getOperationData($project);
        $grantors = ReportUtils::getGrantorData($project);

        //VOLUME
        $reportTextData->content[0]->text = str_replace('_', $procedureData[0], $reportTextData->content[0]->text);

        //INSTRUMENT
        $reportTextData->content[1]->text = str_replace('_', $procedureData[1], $reportTextData->content[1]->text);

        //DATE
        $reportTextData->content[2]->text = str_replace('_', ReportUtils::dateSpanish($procedureData[2]), $reportTextData->content[2]->text);

        //OPERATIONS
        $operationGrantorText = "";

        foreach ($operations as $operation) {
            $operationGrantorText .= $operation . ' ,';
        }

        $operationGrantorText .= "que celebran de una parte como ";

        foreach ($grantors as $grantor) {
            $operationGrantorText .= $grantor[1] . ' ' . $grantor[0] . '; ';
        }

        $reportTextData->content[3]->text = str_replace('_', $operationGrantorText, $reportTextData->content[3]->text);

        //FOLIO
        if (is_null($folio)) {
            $reportTextData->content[9]->show = false;
            $reportTextData->content[10]->show = false;
            $reportTextData->content[11]->show = false;
            $reportTextData->content[12]->show = false;
        }

        // DATA CONFIGURATION
        $dataConfig = ReportUtils::configureData($operations, $grantors);

        $dataConfig[] = [
            'title' => 'procedure',
            'sheets' => $procedureData
        ];

        $reportTextData->data = $dataConfig;

        return $reportTextData;
    }

    public function getDocument(...$args)
    {
        return [
            "data" => $args[0][0],
            "parameters" => [],
            "jasperPath" => Storage::path('reports/deeds/DEEDS.jasper'),
            "output" => Storage::path('reports/deeds/Deeds.docx'),
            "documentType" => "docx",
        ];
    }
}
