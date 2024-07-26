<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\Request;

class ProcedureActionController extends ApiController
{
    public function grantorsAdditionalData(Procedure $procedure, Request $request)
    {
        $this->validate($request, [
            "grantors" => "required|array",
            "grantors.*.grantor_id" => "required|exists:grantors,id",
            "grantors.*.percentage" => "nullable|numeric",
            "grantors.*.amount" => "nullable|numeric",
        ]);

        foreach ($request->input('grantors') as $grantorData) {
            $procedure->grantors()->updateExistingPivot(
                $grantorData['grantor_id'],
                [
                    'percentage' => $grantorData['percentage'] ?? null,
                    'amount' => $grantorData['amount'] ?? null,
                ]
            );
        }

        $procedure->grantors;
        $procedure->documents;

        return $this->showOne($procedure);
    }

    public function expedientRecommendation()
    {
        $lastExpedient = Procedure::orderBy('id', 'desc')->first();

        $recomendedExpedient = [
            'name' => (int)$lastExpedient->name + 1,
        ];

        return $this->showList($recomendedExpedient);
    }

}
