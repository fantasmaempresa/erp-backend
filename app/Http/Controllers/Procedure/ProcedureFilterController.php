<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\CategoryOperation;
use App\Models\Procedure;
use App\Models\Unit;
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
        $procedures = Procedure::all();
        $proceduresFilter = $procedures->filter(function ($value, $key) {
            if (!is_null($value->operation->categoryOperation)) {
                $vulnerable_options = $value->operation->categoryOperation->config['vulnerable'];
                foreach ($vulnerable_options as $vulnerable_option) {
                    switch ($vulnerable_option['type']) {
                        case CategoryOperation::UMA:
                            $uma = Unit::orderBy('id', 'desc')->first();
                            break;
                        case CategoryOperation::UDI:
                            break;
                        case CategoryOperation::DOCUMENT:
                            break;
                        case CategoryOperation::OPTION:
                            break;
                    }
                }
            }
        });
    }
}
