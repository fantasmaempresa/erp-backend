<?php

namespace App\Http\Controllers\Project\PredefinedProjects;

use App\Http\Controllers\ApiController;
use App\Models\Grantor;
use App\Models\Operation;
use App\Models\Procedure;
use App\Models\Stake;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DomainTransferController extends ApiController
{

    public function getPahsesWithFormatR(string $namePashes = null)
    {
        $pahseswithFormat = [
            'generateFirstPreventiveNotice' => [$this, 'getFormatFirstPreventiveNotice'],
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPahsesWithFormat(string $namePashes = null)
    {
        $pahseswithFormat = [
            'generateFirstPreventiveNotice' => [$this, 'generateFirstPreventiveNotice'],
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPhases(string $namePashes = null)
    {
        $pahses = [
            'start' => [$this, 'startProject'],
            'generateFirstPreventiveNotice' => [$this, 'generateFirstPreventiveNotice'],
        ];

        return $namePashes ? $pahses[$namePashes] ?? [] : $pahses;
    }

    static public function getValidatorRequestPhase(string $namePhae): array
    {
        $rules = [
            'start' => [
                'name' => 'required|unique:procedures,name',
                'value_operation' => 'nullable|string|regex:/^[a-zA-Z0-9\s.]+$/',
                'grantors' => 'nullable|array',
                'grantors.*.grantor_id' =>  [
                    'required_if:grantors,!=,null',
                    'exists:grantors,id',
                ],
                'grantors.*.stake_id' => [
                    'required_if:grantors,!=,null',
                    'exists:stakes,id',
                ],
                'operations' => 'required|array',
                'staff_id' => 'required|exists:staff,id',
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

        $procedure = new Procedure($args);
        $procedure->status = Procedure::NO_ACCEPTED;
        $procedure->date = Carbon::now();

        try {
            if (!empty($args['grantors'])) {
                foreach ($args['grantors'] as $item) {
                    $procedure->grantors()->attach($item['grantor']['id'], ['stake_id' => $item['stake']['id']]);
                }
            }

            foreach ($args['operations'] as $operation) {
                $procedure->operations()->attach($operation['id']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('error al almacenar información --> ' . $e->getMessage(), 409);
        }
    }

    public function generateFirstPreventiveNotice(...$args) {
        $project = $args[0][1];
        dd($project);
        $reportTextData = json_decode(Storage::get('reports/first_notice/FirstNotice.json'));

        $operations = Operation::all();
        $stakes = Stake::all();
        $grantors = Grantor::all();

        $reportTextData['data'] = [
            'operations' => $operations,
            'stakes' => $stakes,
            'grantors' => $grantors
        ];

        return $reportTextData;
    }

    public function getFormatFirstPreventiveNotice() {}

    
}
