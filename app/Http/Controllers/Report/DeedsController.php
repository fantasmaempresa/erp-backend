<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DeedsController extends Controller
{
    public function getStructure(...$args)
    {
        $project = $args[0];
        $reportTextData = $this->structure($project);
        
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
        $reportTextData['content'][0]['text'] = str_replace('_', $procedureData[0], $reportTextData['content'][0]['text']);

        //INSTRUMENT
        $reportTextData['content'][1]['text'] = str_replace('_', $procedureData[1], $reportTextData['content'][1]['text']);

        //DATE
        $reportTextData['content'][2]['text'] = str_replace('(1)', ReportUtils::dateSpanish($procedureData[2]), $reportTextData['content'][2]['text']);

        //FOLIO
        if (is_null($folio)) {
            $reportTextData['content'][8]['show'] = false;
            $reportTextData['content'][9]['show'] = false;
            $reportTextData['content'][10]['show'] = false;
            $reportTextData['content'][11]['show'] = false;
        }

        // DATA CONFIGURATION
        $dataConfig = ReportUtils::configureData($operations, $grantors);

        $dataConfig[] = [
            'title' => 'procedure',
            'sheets' => $procedureData
        ];

        $reportTextData['data'] = $dataConfig;

        return $reportTextData;
    }

    public function structure($project)
    {
        $allDeeds = json_decode(Storage::get('reports/deeds/AllDeeds.json'));
        $deeds = collect($allDeeds->deeds);

        //GET ALL OPERATIONS
        $project->procedure->operations->map(function ($operation) use ($deeds, &$deedsData) {
            $operationDeed = $deeds->where("type", $operation->name)->first();
            $deedsData[] = $operationDeed;
        });

        $structure = [
            'title' => 'ESCRITURAS',
            'content' => []
        ];

        $generalData = $deeds->where("type", ReportUtils::FIRST_DATA)->first();
        foreach ($generalData->content as $content) {
            $structure['content'][] = [
                'name' => $content->name,
                'text' => $this->buildText($content->texts),
                'show' => true
            ];
        }

        //INTRODUCTION
        $introduction = '';
        foreach ($deedsData as $deed) {
            $content = collect($deed->content);
            $deedIntroduction = $content->where("name", ReportUtils::INTRODUCTION)->first();
            $introduction .= $this->buildText($deedIntroduction->texts, ' ') . ",";
        }
        $introduction = substr($introduction, 0, -1);

        $structure['content'][2]['text'] = str_replace('(_)', $introduction, $structure['content'][2]['text']);

        $structure['content'] = $this->buildSections($deedsData, ReportUtils::BACKGROUND, $structure['content']);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::STATEMENTS, $structure['content']);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::CLAUSES, $structure['content']);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::PERSONALITY, $structure['content']);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::NOTARIZED, $structure['content']);

        $finalData = $deeds->where("type", ReportUtils::FINAL_DATA)->first();
        foreach ($finalData->content as $content) {
            $structure['content'][] = [
                'name' => $content->name,
                'text' => $this->buildText($content->texts),
                'show' => true
            ];
        }

        return $structure;
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

    private function buildSections($deedsData, $type, $sections)
    {
        $section = '';
        $number = 1;
        foreach ($deedsData as $deed) {
            $content = collect($deed->content);
            $deedContent = $content->where("name", $type)->first();
            if (!is_null($deedContent)) {
                $section .= $this->buildText($deedContent->texts, '<br>', true, $number) . "<br>";
            }
        }

        $sections[] = [
            'name' => $type,
            'text' => $section,
            'show' => !empty($section)
        ];

        return $sections;
    }

    private function buildText($texts, $separator = '<br>', $enumerate = false, &$number = 1)
    {
        $result = '';
        foreach ($texts as $textItem) {
            if ($enumerate) {
                if (Str::contains($textItem, "(_)")) {
                    $result .= str_replace('(_)', $number . '.-', $textItem) . $separator;
                    $number++;
                } else {
                    $result .= $textItem . $separator;
                }
            } else {
                $result .= $textItem . $separator;
            }
        }

        return rtrim($result, $separator);
    }
}
