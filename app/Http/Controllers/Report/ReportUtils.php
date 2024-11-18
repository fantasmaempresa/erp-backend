<?php

namespace App\Http\Controllers\Report;

use App\Models\Project;
use App\Models\Stake;
use Carbon\Carbon;
use NumberFormatter;

class ReportUtils
{
    const FIRST_DATA = 'first_data';
    const FINAL_DATA = 'final_data';
    const INTRODUCTION = 'Introduccion';
    const BACKGROUND = 'Antecedentes';
    const STATEMENTS = 'Declaraciones';
    const CLAUSES = 'Clausulas';
    const PERSONALITY = 'Personalidad';
    const NOTARIZED = 'Yo, la Notario certifico';

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

    static function operationTextReplace($operations)
    {
        $operationText = (count($operations) > 1) ? 'las operaciones de ' : 'la operación de ';
        $asciiList = 97;
        foreach ($operations as $operation) {
            $operationText .= chr($asciiList) . ').- ' . $operation . ' ';
            $asciiList++;
        }
        $operationText .= ', ';

        return $operationText;
    }

    static function grantorTextReplace($grantors)
    {
        $grantorText = "";
        foreach ($grantors as $grantor) {
            $grantorText .= $grantor[1] . ': ' . strtoupper($grantor[2]) . ' ' . $grantor[0] . " <br> <br>";
        }

        return $grantorText;
    }

    static function configureData($operations, $grantors)
    {
        $dataConfig = [];
        $dataOperation['title'] = 'operations';
        $dataOperation['sheets'] = $operations->toArray();
        $dataConfig[] = $dataOperation;

        $grantorNumber = 1;
        foreach ($grantors as $grantor) {
            $dataGrantor['title'] = 'gantors' . $grantorNumber;
            $dataGrantor['sheets'] = $grantor;
            $dataConfig[] = $dataGrantor;
            $grantorNumber++;
        }

        return $dataConfig;
    }

    static function dateSpanish($date)
    {
        $carbonDate = Carbon::parse($date);
        $carbonDate->locale('es');

        $daysLetter = self::numberSpanish($carbonDate->day);
        $yearLetter = self::numberSpanish($carbonDate->year);

        return "a los " . $daysLetter . " días del mes de " . $carbonDate->isoFormat('MMMM') . " del año " . $yearLetter;
    }

    static function numberSpanish($number)
    {
        $numberFormatter = new NumberFormatter('es_ES', \NumberFormatter::SPELLOUT);
        return $numberFormatter->format($number);
    }
}
