<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FirstPreventiveNoticeController extends Controller
{
    public function getStructure(...$args)
    {
        $project = $args[0];
        
        $reportTextData = json_decode(Storage::get('reports/first_notice/FirstNotice.json'));

        $operations = ReportUtils::getOperationData($project);

        $grantors = ReportUtils::getGrantorData($project);

        // PLACE
        $reportTextData->content[1]->text = $reportTextData->content[1]->text . $project->procedure->place->name;

        // OPERATIONS
        $reportTextData->content[3]->text = str_replace('_', ReportUtils::operationTextReplace($operations), $reportTextData->content[3]->text);

        // GRANTORS
        $reportTextData->content[4]->text = ReportUtils::grantorTextReplace($grantors);

        // EXPEDIENT
        $reportTextData->content[11]->text = str_replace('_', $project->procedure->name, $reportTextData->content[11]->text);

        // DATA CONFIGURATION
        $dataConfig = ReportUtils::configureData($operations, $grantors);

        $dataConfig[] = [
            'title' => 'place',
            'sheets' => [$project->procedure->place->name]
        ];

        //PROCEDURE DATA
        $procedureData = array_values(array_filter([
            $project->procedure->name,
            $project->procedure->value_operation,
            $project->procedure->date_proceedings,
            $project->procedure->date,
            $project->procedure->credit,
            $project->procedure->observation,
            $project->procedure->status
        ]));

        $dataConfig[] = [
            'title' => 'expedient',
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
            "jasperPath" => Storage::path('reports/first_notice/FirstNotice.jasper'),
            "output" => Storage::path('reports/first_notice/FirstNotice.rtf'),
            "documentType" => "rtf",
        ];
    }
}
