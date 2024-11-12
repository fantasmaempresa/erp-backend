<?php

namespace App\Http\Controllers\Project\PredefinedProjects;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Folio\FolioUtil;
use App\Http\Controllers\Report\DeedsController;
use App\Http\Controllers\Report\FirstPreventiveNoticeController;
use App\Http\Controllers\Report\SecondPreventiveNoticeController;
use App\Models\Folio;
use App\Models\Procedure;
use App\Models\ReportConfiguration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DomainTransferController extends ApiController
{

    public function getPahsesWithFormatReport(string $namePashes = null)
    {
        $pahseswithFormat = [
            'getFormatFirstPreventiveNotice' => [$this, 'getFormatFirstPreventiveNotice'],
            'getFormatSecondPreventiveNotice' => [$this, 'getFormatSecondPreventiveNotice'],
            'getFormatBuySell' => [$this, 'getFormatBuySell'],
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPahsesWithFormat(string $namePashes = null)
    {
        $pahseswithFormat = [
            'generateFirstPreventiveNotice' => [$this, 'generateFirstPreventiveNotice'],
            'generateSecondPreventiveNotice' => [$this, 'generateSecondPreventiveNotice'],
            'generateBuySell' => [$this, 'generateBuySell'],
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPhases(string $namePashes = null)
    {
        $pahses = [
            'start' => [$this, 'startProject'],
            'generateFirstPreventiveNotice' => [$this, 'generateFirstPreventiveNotice'],
            'getFormatFirstPreventiveNotice' => [$this, 'getFormatFirstPreventiveNotice'],
            'generateSecondPreventiveNotice' => [$this, 'generateSecondPreventiveNotice'],
            'getFormatSecondPreventiveNotice' => [$this, 'getFormatSecondPreventiveNotice'],
            'generateFolio' => [$this, 'generateFolio'],
            'generateBuySell' => [$this, 'generateBuySell'],
            'getFormatBuySell' => [$this, 'getFormatBuySell'],
            'generateShape' => [$this, 'generateShape'],
        ];

        return $namePashes ? $pahses[$namePashes] ?? [] : $pahses;
    }

    static public function getValidatorRequestPhase(string $namePhae): array
    {
        $rules = [
            'start' => [
                'data.name' => 'required|unique:procedures,name',
                'data.value_operation' => 'nullable|string|regex:/^[a-zA-Z0-9\s.]+$/',
                'data.grantors' => 'nullable|array',
                'data.grantors.*.grantor_id' =>  [
                    'required_if:grantors,!=,null',
                    'exists:grantors,id',
                ],
                'data.grantors.*.stake_id' => [
                    'required_if:grantors,!=,null',
                    'exists:stakes,id',
                ],
                'data.operations' => 'required|array',
                'data.staff_id' => 'required|exists:staff,id',
            ],
            'generateFolio' => [
                'data.name' => 'required|int|unique:folios,name',
                'data.folio_min' => 'required|int|unique:folios,folio_min',
                'data.folio_max' => 'required|int|unique:folios,folio_max',
                'data.book_id' => 'required|int',
                'data.procedure_id' => 'required|int|unique:folios,procedure_id',
            ],
            'generateSecondPreventiveNotice' => [
                'data.lasted_related_report_id' => 'required|int|exists:report_configurations,id',
                'data.last_report_id' => 'nullable|int|exists:report_configurations,id'
            ],
            'generateFirstPreventiveNotice' => [
                'data.last_report_id' => 'nullable|int|exists:report_configurations,id'
            ],
            'generateBuySell' => [
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

    public function startProject(array $args)
    {
        $data = $args['data'];
        $procedure = new Procedure($data);
        $procedure->status = Procedure::IN_PROCESS;
        $procedure->date = Carbon::now();
        $procedure->date_appraisal = $procedure->date_appraisal ? Carbon::parse($procedure->date_appraisal) : null;
        $procedure->client_id = $args['project']->client_id;
        $procedure->staff_id = $args['project']->staff_id;
        $procedure->user_id = Auth::id();
        DB::begintransaction();
        try {
            $procedure->save();
            if (!empty($data['grantors'])) {
                foreach ($data['grantors'] as $item) {
                    $procedure->grantors()->attach($item['grantor']['id'], ['stake_id' => $item['stake']['id']]);
                }
            }

            foreach ($data['operations'] as $operation) {
                $procedure->operations()->attach($operation['id']);
            }

            $args['project']->procedure_id = $procedure->id;
            $args['project']->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('error al almacenar información --> ' . $e->getMessage(), 409);
        }

        return $this->showOne($procedure);
    }

    // FIRST PREVENTIVE NOTICE REPORT
    public function generateFirstPreventiveNotice(...$args)
    {
        $firstPreventiveNotice = new FirstPreventiveNoticeController();
        return $firstPreventiveNotice->getStructure(...$args);
    }

    public function getFormatFirstPreventiveNotice(...$args)
    {
        
        $firstPreventiveNotice = new FirstPreventiveNoticeController();
        return $firstPreventiveNotice->getDocument($args);
    }
    // END FIRST PREVENTIVE NOTICE REPORT

    // DEEDS REPORT
    public function generateBuySell(...$args)
    {
        $buySell = new DeedsController();
        return $buySell->structure(...$args);
    }

    public function getFormatBuySell(...$args) {
        $buySell = new DeedsController();
        return $buySell->getDocument($args);
    }
    // END DEEDS REPORT

    // SECOND PREVENTIVE NOTICE REPORT
    public function generateSecondPreventiveNotice(...$args)
    {
        $secondPreventiveNotice = new SecondPreventiveNoticeController();
        return $secondPreventiveNotice->getStructure(...$args);
    }

    public function getFormatSecondPreventiveNotice(...$args)
    {

        if (isset($args['last_report_id'])){
            $lastReport = ReportConfiguration::findOrFail($args['last_report_id']);
            if (!$lastReport) {
                return $this->errorResponse('El reporte de condición inicial no existe', 404);
            }
            return $this->showList($lastReport->data);
        }
        
        $secondPreventiveNotice = new SecondPreventiveNoticeController();

        return $secondPreventiveNotice->getDocument($args);
    }
    // END SECOND PREVENTIVE NOTICE REPORT

    public function generateFolio(array $args)
    {
        $folio = new Folio($args['data']);
        $folio->user_id = Auth::user()->id;

        if (empty($args['project']->procedure_id)) {
            return $this->errorResponse('El proyecto no tiene asociado un procedimiento', 404);
        }

        $folio->procedure_id = $args['project']->procedure_id;
        if (FolioUtil::verifyRangeFolio(new Folio(), $folio->folio_min, $folio->folio_max)) {
            if (FolioUtil::validateFolioRangeInBook($folio->folio_min, $folio->folio_max, $folio->book_id)) {
                $folio->save();
            } else {
                return $this->errorResponse('Los folios estan fuera de rango de este libro', 422);
            }
        } else {
            return $this->errorResponse('El rango de folio está ocupado por otro instrumento', 422);
        }

        return $this->showOne($folio);
    }
}
