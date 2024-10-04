<?php

namespace App\Http\Controllers\Report;

use App\Models\Stake;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FirstPreventiveNoticeController extends Controller
{
    public function getStructure(...$args)
    {
        $project = $args[0];
        $reportTextData = json_decode(Storage::get('reports/first_notice/FirstNotice.json'));

        $operations = $project->procedure->operations->map(function ($operation) {
            return $operation->name;
        });

        $grantors = $project->procedure->grantors->map(function ($grantor) {
            $stake = Stake::find($grantor->pivot->stake_id);

            return [
                $grantor->name .
                    ((!is_null($grantor->father_last_name) || $grantor->father_last_name != "BK") ? '' : ' ' . $grantor->father_last_name) .
                    ((!is_null($grantor->mother_last_name) || $grantor->mother_last_name != "BK") ? '' : ' ' . $grantor->mother_last_name),
                $stake->name,
                $grantor->occupation,
                $grantor->email,
                $grantor->rfc,
                $grantor->curp,
                $grantor->civil_status,
                $grantor->municipality,
                $grantor->colony,
                $grantor->street,
                $grantor->no_int,
                $grantor->no_ext,
                $grantor->no_locality,
                $grantor->phone,
                $grantor->locality,
                $grantor->zipcode,
                $grantor->place_of_birth,
                $grantor->birthdate,
                $grantor->economic_activity,
            ];
        });

        // PLACE
        $reportTextData->content[1]->text = $reportTextData->content[1]->text . $project->procedure->place->name;

        // OPERATIONS
        $operationText = (count($operations) > 1) ? 'las operaciones de ' : 'la operación de ';
        $asciiList = 97;
        foreach ($operations as $operation) {
            $operationText .= chr($asciiList) . ').- ' . $operation . ' ';
            $asciiList++;
        }
        $operationText .= ', ';
        $reportTextData->content[3]->text = str_replace('_', $operationText, $reportTextData->content[3]->text);

        // GRANTORS
        $grantorText = "";
        foreach ($grantors as $grantor) {
            $grantorText .= $grantor[1] . ': ' . strtoupper($grantor[2]) . ' ' . $grantor[0] . " \n \n";
        }
        $reportTextData->content[4]->text = $grantorText;

        // EXPEDIENT
        $reportTextData->content[11]->text = str_replace('_', $project->procedure->name, $reportTextData->content[11]->text);

        // DATA CONFIGURATION
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

        $dataConfig[] = [
            'title' => 'place',
            'sheets' => [$project->procedure->place->name]
        ];

        $dataConfig[] = [
            'title' => 'expedient',
            'sheets' => [$project->procedure->name]
        ];

        $reportTextData->data = $dataConfig;

        return $reportTextData;
    }

    public function getDocument()
    {
        return [
            "parameters" => [],
            "jasperPath" => Storage::path('reports/first_notice/FirstNotice.jasper'),
            "output" => Storage::path('reports/first_notice/FirstNotice.rtf'),
            "documentType" => "rtf",
        ];
    }
}
