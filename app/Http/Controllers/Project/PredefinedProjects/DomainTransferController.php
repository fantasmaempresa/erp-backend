<?php

namespace App\Http\Controllers\Project\PredefinedProjects;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DomainTransferController extends ApiController
{

    public function getPahsesWithFormat(string $namePashes = null)
    {
        $pahseswithFormat = [
            'generateFirstPreventiveNotice' => [$this, 'getFormatFirstPreventiveNotice'],
        ];

        return $namePashes ? $pahseswithFormat[$namePashes] ?? [] : $pahseswithFormat;
    }

    public function getPhases(string $namePashes = null)
    {
        $pahses = [
            'start' => [$this, 'startProject'],
            'generateFirstPreventiveNotice' => [$this, 'startProject'],
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

    public function generateFirstPreventiveNotice(array $args) {}

    public function getFormatFirstPreventiveNotice() {}
}
