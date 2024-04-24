<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcedureFilterController extends ApiController
{
    public function myProcedures(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = Procedure::where('procedures.user_id', Auth::id())
                ->search($request->get('search'))
                ->with('grantors.stake')
                ->with('documents')
                ->with('client')
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        } else {
            $response = Procedure::where('user_id', Auth::id())
                ->with('grantors')
                ->with('documents')
                ->with('client')
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        }

        return $this->showList($response);
    }

    public function proceduresWithoutData(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = Procedure::leftJoin('registration_procedure_data', 'procedures.id', '=', 'registration_procedure_data.procedure_id')
                ->where('registration_procedure_data.procedure_id', null)
                ->select('procedures.*')
                ->search($request->get('search'))
                ->with('grantors.stake')
                ->with('documents')
                ->with('client')
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        } else {
            $response = Procedure::leftJoin('registration_procedure_data', 'procedures.id', '=', 'registration_procedure_data.procedure_id')
                ->where('registration_procedure_data.procedure_id', null)
                ->select('procedures.*')
                ->with('grantors.stake')
                ->with('documents')
                ->with('client')
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        }

        return $this->showList($response);
    }

    public function proceduresVulnerableOperations(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = Procedure::leftJoin('vulnerable_operations', 'procedures.id', '=', 'vulnerable_operations.procedure_id')
                ->where('vulnerable_operations.procedure_id', '!=',null)
                ->select('procedures.*')
                ->search($request->get('search'))
                ->with('grantors.stake')
                ->with('documents')
                ->with('client')
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        } else{
            $response = Procedure::leftJoin('vulnerable_operations', 'procedures.id', '=', 'vulnerable_operations.procedure_id')
                ->where('vulnerable_operations.procedure_id', '!=',null)
                ->select('procedures.*')
                ->with('grantors.stake')
                ->with('documents')
                ->with('client')
                ->orderBy('id', 'desc')
                ->paginate($paginate);
        }

        return $this->showList($response);
    }
}
