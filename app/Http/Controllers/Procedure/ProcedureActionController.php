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
        $lastExpedients = Procedure::where('user_id', '<>', 6)->orderBy('id', 'desc')->get();
        $auxExpediente =  new Procedure();
        foreach ($lastExpedients as $lastExpedient) {
            if ((int)$lastExpedient->name > (int)$auxExpediente->name)
                $auxExpediente = $lastExpedient;
        }

        $recomendedExpedient = [
            'name' => (int)$auxExpediente->name + 1,
        ];

        return $this->showList($recomendedExpedient);
    }

    public function notPassedExpedient(Request $request)
    {
        $this->validate($request, [
            "id" => "required|exists:procedures,id",
            "description" => "required|string",
        ]);

        $procedure =  Procedure::findOrFail($request->get('id'));
        $procedure->load('folio');
        if ($procedure->status === Procedure::NO_ACCEPTED){
            return $this->errorResponse('El expediente ya fue cancelado', 422);
        }

        if ($procedure->folio){
            return $this->errorResponse('No se puede cancelar un expediente con un instrumento asignado', 422);
        }

        $procedure->status = Procedure::NO_ACCEPTED;
        $procedure->observation = $procedure->observation . "\n --- NO PASO: " . $request->get('description') . ' --- ';
        $procedure->save();

        return $this->showOne($procedure);
    }
}
