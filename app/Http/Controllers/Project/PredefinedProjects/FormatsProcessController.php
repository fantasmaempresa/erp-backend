<?php

namespace App\Http\Controllers\Project\PredefinedProjects;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Report\CancellationFirstPreventNoticeController;
use App\Http\Controllers\Report\ClarificationNoticeController;

class FormatsProcessController extends Controller
{
    public function getPahsesWithFormatReport(string $namePashes = null)
    {
        $pahseswithFormat = [
            'getFormatClarificationNotice' => [$this, 'getFormatClarificationNotice'],
            'getFormatCancellationFirstPreventNotice' => [$this, 'getFormatClarificationNotice']
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPahsesWithFormat(string $namePashes = null)
    {
        $pahseswithFormat = [
            'generateClarificationNotice' => [$this, 'generateClarificationNotice'],
            'generateCancellationFirstPreventNotice' => [$this, 'generateClarificationNotice'],
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPhases(string $namePashes = null)
    {
        $pahses = [
            'generateClarificationNotice' => [$this, 'generateClarificationNotice'],
            'getFormatClarificationNotice' => [$this, 'getFormatClarificationNotice'],
            'generateCancellationFirstPreventNotice' => [$this, 'generateCancellationFirstPreventNotice'],
            'getFormatCancellationFirstPreventNotice' => [$this, 'getFormatCancellationFirstPreventNotice'],
        ];

        return $namePashes ? $pahses[$namePashes] ?? [] : $pahses;
    }

    static public function getValidatorRequestPhase(string $namePhae): array
    {

        $rules = [
            'generateClarificationNotice' => [
                'data.lasted_related_report_id' => 'required|int|exists:report_configurations,id',
                'data.last_report_id' => 'nullable|int|exists:report_configurations,id'
            ],
            'getFormatClarificationNotice' => [
                'data.last_report_id' => 'nullable|int|exists:report_configurations,id'
            ],
            'generateCancellationFirstPreventNotice' => [
                'data.lasted_related_report_id' => 'required|int|exists:report_configurations,id',
                'data.last_report_id' => 'nullable|int|exists:report_configurations,id'
            ],
            'getFormatCancellationFirstPreventNotice' => [
                'data.last_report_id' => 'nullable|int|exists:report_configurations,id'
            ],  
        ];

        return $rules[$namePhae] ?? [];
    }


    public function executePhase(string $phaseName, ...$args)
    {
        $phases = $this->getPhases();

        if (array_key_exists($phaseName, $phases))
            return call_user_func_array($phases[$phaseName], $args);
        else
            return false;
    }

    // AVISO ACLARATORIO
    public function generateClarificationNotice(...$args)
    {
        $firstPreventiveNotice = new ClarificationNoticeController();
        return $firstPreventiveNotice->getStructure(...$args);
    }

    public function getFormatClarificationNotice(...$args)
    {

        $firstPreventiveNotice = new ClarificationNoticeController();
        return $firstPreventiveNotice->getDocument($args);
    }
    // END AVISO ACLARATORIO

    // CANCELACIÓN DE PRIMER AVISO PREVENTIVO
    public function generateCancellationFirstPreventNotice(...$args)
    {
        $firstPreventiveNotice = new CancellationFirstPreventNoticeController();
        return $firstPreventiveNotice->getStructure(...$args);
    }

    public function getFormatCancellationFirstPreventNotice(...$args)
    {

        $firstPreventiveNotice = new CancellationFirstPreventNoticeController();
        return $firstPreventiveNotice->getDocument($args);
    }
    //END CANCELACIÓN DE PRIMER AVISO PREVENTIVO
}
