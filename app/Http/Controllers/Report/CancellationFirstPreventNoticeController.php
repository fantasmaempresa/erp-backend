<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\ReportConfiguration;


class CancellationFirstPreventNoticeController extends Controller
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
        $reportTextData = json_decode(Storage::get('reports/CancellationFirstPrevent/CancellationFirstPrevent.json'));
        
        foreach ($lasted_related_report->data['content'] as $content) {
            if ($content['id'] == 3) $BODY = $content['text'];
            if ($content['id'] == 4) $GRANTORS = $content['text'];
            if ($content['id'] == 5) $PROPERTY = $content['text'];
            if ($content['id'] == 6) $REGISTRATION = $content['text'];
            if ($content['id'] == 7) {
                $dateLastReport = explode(',', $content['text']);
                $dateLastReport = str_replace(['<p>', '</p>'], '', $dateLastReport[1]);
            }
        }

        $operations = ReportUtils::getOperationData($project);

        $grantors = ReportUtils::getGrantorData($project);

        $reportTextData->content[1]->text = $reportTextData->content[1]->text . $project->procedure->place->name;

        //Fecha
        

        $reportTextData->content[3]->text = str_replace(
            '[OPERATIONS]',
            ReportUtils::operationTextReplace($operations),
            $reportTextData->content[3]->text
        );

        $reportTextData->content[3]->text = str_replace(
            '[DATE]',
            $dateLastReport,
            $reportTextData->content[3]->text
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

        $reportTextData->content[5]->text = str_replace(
            '[DATE]',
            ReportUtils::dateSpanish(),
            $reportTextData->content[5]->text
        );

        $reportTextData->content[9]->text = str_replace('_', $project->procedure->name, $reportTextData->content[9]->text);

        // DATA CONFIGURATION
        $dataConfig = ReportUtils::configureData($operations, $grantors);

        $dataConfig[] = [
            'title' => 'place',
            'sheets' => [$project->procedure->place->name]
        ];

        $dataConfig[] = [
            'title' => 'Notarias',
            'sheets' => [
                'Dra. Norma Romero Cortés', 
                'Notario Público Titular', 
                'Lic. Norma Alma Cortés Caballero',  
                'Notario Público Auxiliar'
            ]
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
            "jasperPath" => Storage::path('reports/CancellationFirstPrevent/CancellationFirstPrevent.jasper'),
            "output" => Storage::path('reports/CancellationFirstPrevent/CancellationFirstPrevent.docx'),
            "documentType" => "docx",
        ];
    }
}
