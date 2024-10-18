<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BuySellController extends Controller
{
    public function getStructure(...$args)
    {
        $project = $args[0];
        $reportTextData = json_decode(Storage::get('reports/buy_sell/BuySell.json'));

        $procedureData = [
            "book" => number_format(str_replace('_', '', $project->procedure->folio->book->name), 0, '.', ','),
            "instrument" => number_format(str_replace('_', '', $project->procedure->folio->name), 0, '.', ','),
        ];

        $operations = ReportUtils::getOperationData($project);
        $grantors = ReportUtils::getGrantorData($project);

        //VOLUME
        $reportTextData->content[0]->text = str_replace('_', $procedureData["book"], $reportTextData->content[0]->text);

        //INSTRUMENT
        $reportTextData->content[1]->text = str_replace('_', $procedureData["instrument"], $reportTextData->content[1]->text);

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

        // DATA CONFIGURATION
        $dataConfig = ReportUtils::configureData($operations, $grantors);

        $dataConfig[] = [
            'title' => 'procedure',
            'sheets' => [$procedureData["book"], $procedureData["instrument"]]
        ];

        $reportTextData->data = $dataConfig;

        return $reportTextData;
    }

    public function getDocument()
    {
        return [
            "parameters" => [],
            "jasperPath" => Storage::path('reports/buy_sell/BUY_SELL.jasper'),
            "output" => Storage::path('reports/buy_sell/BuySell.rtf'),
            "documentType" => "rtf",
        ];
    }
}
