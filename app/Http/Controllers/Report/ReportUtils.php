<?php

namespace App\Http\Controllers\Report;

use App\Models\Project;
use App\Models\Stake;

class ReportUtils
{
    static function getOperationData(Project $project)
    {
        return $project->procedure->operations->map(function ($operation) {
            return $operation->name;
        });
    }

    static function getGrantorData(Project $project)
    {
        return $project->procedure->grantors->map(function ($grantor) {
            $stake = Stake::find($grantor->pivot->stake_id);

            return array_values(array_filter([
                $grantor->name .
                    ((isset($grantor->father_last_name) && $grantor->father_last_name !== "BK") ? ' ' . $grantor->father_last_name : '') .
                    ((isset($grantor->mother_last_name) && $grantor->mother_last_name !== "BK") ? ' ' . $grantor->mother_last_name : ''),
                $stake->name ?? '-',
                $grantor->occupation ?? '-',
                $grantor->email ?? '',
                $grantor->rfc ?? '',
                $grantor->curp ?? '',
                $grantor->civil_status ?? '',
                $grantor->municipality ?? '',
                $grantor->colony ?? '',
                $grantor->street ?? '',
                $grantor->no_int ?? '',
                $grantor->no_ext ?? '',
                $grantor->no_locality ?? '',
                $grantor->phone ?? '',
                $grantor->locality ?? '',
                $grantor->zipcode ?? '',
                $grantor->place_of_birth ?? '',
                $grantor->birthdate ?? '',
                $grantor->economic_activity ?? ''
            ], function ($value) {
                return !is_null($value) && $value !== '' && $value !== 'bk';
            }));
        });
    }
}
