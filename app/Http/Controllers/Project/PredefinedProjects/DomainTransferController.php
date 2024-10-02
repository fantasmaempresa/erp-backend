<?php

namespace App\Http\Controllers\Project\PredefinedProjects;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use App\Models\Stake;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DomainTransferController extends ApiController
{

    public function getPahsesWithFormatReport(string $namePashes = null)
    {
        $pahseswithFormat = [
            'getFormatFirstPreventiveNotice' => [$this, 'getFormatFirstPreventiveNotice'],
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
            'getFormatFirstPreventiveNotice' => [$this, 'getFormatFirstPreventiveNotice'],
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
        $data = $args['data'];
        $procedure = new Procedure($data);
        $procedure->status = Procedure::IN_PROCESS;
        $procedure->date = Carbon::now();
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

    public function generateFirstPreventiveNotice(...$args) {
        $project = $args[0];
        $reportTextData = json_decode(Storage::get('reports/first_notice/FirstNotice.json'));

        $operations = $project->procedure->operations->map(function ($operation) {
            return [
                'name' => $operation->name,
                'description' => $operation->description,
            ];
        });
        $grantors = $project->procedure->grantors->map(function ($grantor) {
            $stake = Stake::find($grantor->pivot->stake_id);

            return [
                'name' => $grantor->name,
                'father_last_name' => $grantor->father_last_name,
                'mother_last_name' => $grantor->mother_last_name,
                'satke' => [
                    'name' => $stake->name
                ]
            ];
        });

        $reportTextData->data = [
            "operations" => $operations,
            "grantors" => $grantors
        ];

        return $reportTextData;
    }

    public function getFormatFirstPreventiveNotice() {
        return [
            "parameters" => [],
            "jasperPath" => Storage::path('reports/first_notice/FirstNotice.jasper'),
            "output" => Storage::path('reports/first_notice/FirstNotice.rtf'),
            "documentType" => "rtf",
        ];
    }
}
