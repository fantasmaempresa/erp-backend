<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Report\Deed\DeedFolioController;
use App\Http\Controllers\Report\Deed\DeedProjectController;
use App\Http\Controllers\Report\Deed\DeedTestimonyController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DeedsController extends Controller
{
    const TESTIMONY = 1;
    const FOLIO = 2;
    const PROJECT = 3;


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
        $deedsData = [];

        //GET ALL OPERATIONS
        $project->procedure->operations->map(function ($operation) use ($deeds, &$deedsData) {
            $operationDeed = $deeds->where("type", $operation->name)->first();
            if (!is_null($operationDeed)) {
                $deedsData[] = $operationDeed;
            }
        });

        $structure = [
            'title' => 'ESCRITURAS',
            'content' => []
        ];

        $id = 0;
        $structure = $this->buildGeneralSections($deeds, $structure, ReportUtils::FIRST_DATA, $id);

        //INTRODUCTION
        $introduction = '';
        foreach ($deedsData as $deed) {
            $content = collect($deed->content);
            $deedIntroduction = $content->where("name", ReportUtils::INTRODUCTION)->first();
            $introduction .= $this->buildText($deedIntroduction->texts, ' ') . ",";
        }
        $introduction = substr($introduction, 0, -1);

        $structure['content'][2]['text'] = str_replace('(_)', $introduction, $structure['content'][2]['text']);

        $structure['content'] = $this->buildSections($deedsData, ReportUtils::BACKGROUND, $structure['content'], $id);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::STATEMENTS, $structure['content'], $id);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::CLAUSES, $structure['content'], $id);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::PERSONALITY, $structure['content'], $id);
        $structure['content'] = $this->buildSections($deedsData, ReportUtils::NOTARIZED, $structure['content'], $id);

        $structure = $this->buildGeneralSections($deeds, $structure, ReportUtils::FINAL_DATA, $id);

        return $structure;
    }

    public function getDocument(...$args)
    {
        $data = $args[0];

        if (!isset($data['stage'])) {
            $deedTestimonyController = new DeedTestimonyController();
            return $deedTestimonyController->documentParams($data);
        }

        switch ($data['stage']) {
            case self::TESTIMONY:
                $controller = new DeedTestimonyController();
                break;
            case self::FOLIO:
                $controller = new DeedFolioController();
                break;
            case self::PROJECT:
                $controller = new DeedProjectController();
                break;
            default:
                $controller = new DeedTestimonyController();
                break;
        }

        return $controller->documentParams($data);
    }

    private function buildGeneralSections($deeds, $structure, $type, &$id)
    {
        $generalSection = $deeds->where("type", $type)->first();
        foreach ($generalSection->content as $content) {
            $structure['content'][] = [
                'id' => $id,
                'name' => $content->name,
                'text' => $this->buildText($content->texts),
                'show' => true
            ];
            $id++;
        }

        return $structure;
    }

    private function buildSections($deedsData, $type, $sections, &$id)
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
            'id' => $id,
            'name' => $type,
            'text' => $section,
            'show' => !empty($section)
        ];

        $id++;
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
