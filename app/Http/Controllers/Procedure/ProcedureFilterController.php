<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\CategoryOperation;
use App\Models\InversionUnit;
use App\Models\Procedure;
use App\Models\Unit;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $blockSize = 500;

        $procedureResults = [];


        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $procedures = Procedure::search($request->get('search'))->with([
                'user',
                'grantors.stake',
                'documents',
                'client',
                'operations',
                'comments',
                'registrationProcedureData',
                'processingIncome'
            ]);
        } else {
            $procedures = Procedure::query()->with(
                [
                    'user',
                    'grantors.stake',
                    'documents',
                    'client',
                    'operations',
                    'comments',
                    'registrationProcedureData',
                    'processingIncome'
                ]
            );
        }

        $procedures->chunk($blockSize, function ($registers) use (&$procedureResults) {
            foreach ($registers as $register) {
                if ($this->analyseProcedure($register)) {
                    $procedureResults[] = $register;
                }
            }
        });

        $page = $request->input('page', 1);
        $offset = ($page - 1) * $paginate;
        $itemsPaginados = array_slice($procedureResults, $offset, $paginate);
        $paginador = new LengthAwarePaginator($itemsPaginados, count($procedureResults), $paginate, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $this->showList($paginador);
    }

    private function analyseProcedure($procedure)
    {
        $vulnerable = false;
        foreach ($procedure->operations as $operation) {
            if (!is_null($operation->categoryOperation)) {
                $vulnerableOptions = $operation->categoryOperation->config['vulnerable'];
                foreach ($vulnerableOptions as $vulnerableOption) {
                    switch ($vulnerableOption['type']) {
                        case CategoryOperation::UMA:
                            $uma = Unit::orderBy('id', 'desc')->first();
                            if (!is_null($uma)) {
                                $umaValue = $uma->value * $vulnerableOption['condition'];
                                $vulnerable = (int)$procedure->value_operation > $umaValue;
                            }
                            break;
                        case CategoryOperation::UDI:
                            $udi = InversionUnit::orderBy('id', 'desc')->first();
                            if (!is_null($udi)) {
                                $udiValue = $uma->value * $vulnerableOption['condition'];
                                $vulnerable = (int)$procedure->value_operation > $udiValue;
                            }
                            break;
                        case CategoryOperation::DOCUMENT:
                            foreach ($vulnerableOption['condition'] as $condition) {
                                if (!$procedure->documents->contains('id', $condition["id"])) {
                                    $vulnerable = true;
                                }
                            }
                            break;
                        case CategoryOperation::OPTION:
                            $vulnerable = true;
                            break;
                    }
                }
            }
        }

        return $vulnerable;
    }
}
