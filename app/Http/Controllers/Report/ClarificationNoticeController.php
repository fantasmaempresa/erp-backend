<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ReportConfiguration;
use Illuminate\Support\Facades\Storage;


class ClarificationNoticeController extends Controller
{
    /**
     * $args[0]: Project Object
     * $args[1]: Process Object
     * $args[2]: Data Request Object
     */
    public function getStructure(...$args)
    {

        $project = $args[0];
        $lasted_related_report = ReportConfiguration::findOrFail($args[2]['lasted_related_report_id']);
        $reportTextData = json_decode(Storage::get('reports/ClarificationNotice/ClarificationNotice.json'));


        $operations = ReportUtils::getOperationData($project);

        $grantors = ReportUtils::getGrantorData($project);

        $reportTextData->content[0]->text = str_replace(
            '[RELATED]',
            ReportUtils::getNameReport(
                $lasted_related_report->name_phase,
                $lasted_related_report->name_process,
            ),
            $reportTextData->content[0]->text
        );

        $reportTextData->content[1]->text = $reportTextData->content[1]->text . $project->procedure->place->name;

        //Fecha
        $reportTextData->content[3]->text =  str_replace(
            '[RELATED]',
            ReportUtils::getNameReport(
                $lasted_related_report->name_phase,
                $lasted_related_report->name_process,
            ),
            $reportTextData->content[3]->text
        );

        $reportTextData->content[3]->text = str_replace(
            '[OPERATION]',
            ReportUtils::operationTextReplace($operations),
            $reportTextData->content[3]->text
        );

        $reportTextData->content[3]->text = str_replace(
            '[DATE]',
            ReportUtils::dateSpanish(),
            $reportTextData->content[3]->text
        );

        foreach ($lasted_related_report->data['content'] as $content) {
            if ($content['id'] == 3 ) $BODY = $content['text'];
            if ($content['id'] == 4 ) $GRANTORS = $content['text'];
            if ($content['id'] == 5 ) $PROPERTY = $content['text'];
            if ($content['id'] == 6 ) $REGISTRATION = $content['text'];
        }

        $reportTextData->content[4]->text = str_replace(
            '[BODY]',
            $BODY,
            $reportTextData->content[4]->text
        );

        $reportTextData->content[4]->text = str_replace(
            '[GRANTORS]',
            $GRANTORS,
            $reportTextData->content[4]->text
        );

        $reportTextData->content[4]->text = str_replace(
            '[PROPERTY]',
            $PROPERTY,
            $reportTextData->content[4]->text
        );
        $reportTextData->content[4]->text = str_replace(
            '[REGISTRATION]',
            $REGISTRATION,
            $reportTextData->content[4]->text
        );
        
        $reportTextData->content[7]->text = str_replace(
            '[DATE]',
            ReportUtils::dateSpanish(),
            $reportTextData->content[7]->text
        );

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
            "jasperPath" => Storage::path('reports/ClarificationNotice/ClarificationNotice.jasper'),
            "output" => Storage::path('reports/ClarificationNotice/ClarificationNotice.docx'),
            "documentType" => "docx",
        ];
    }
}
