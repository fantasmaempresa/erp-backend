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

        $procedure->grantors()->sync($request->get("grantors"));

        $procedure->grantors;
        $procedure->documents;
        
        return $this->showOne($procedure);
    }
}
