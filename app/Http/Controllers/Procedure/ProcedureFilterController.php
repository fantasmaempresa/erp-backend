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

        $procedures = Procedure::with('grantors.stake')
            ->with('documents')
            ->with('client')
            ->orderBy('id', 'desc')->get();

        $filteredProcedures = $procedures->filter(function ($procedure) use ($request) {
            $vulnerable = false;
            //CHECK VULNERABLE OPERATIONS
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

            // SEARCH
            $searchTerm = strtoupper($request->get('search'));
            if (!empty($searchTerm) && $searchTerm !== 'null' && $vulnerable) {
                $search = str_contains($procedure->name, $searchTerm) ||
                    str_contains($procedure->value_operation, $searchTerm) ||
                    str_contains($procedure->instrument, $searchTerm) ||
                    str_contains($procedure->date, $searchTerm) ||
                    str_contains($procedure->volume, $searchTerm) ||
                    str_contains($procedure->folio_min, $searchTerm) ||
                    str_contains(($procedure->client->name . ' ' . $procedure->client->last_name . ' ' . $procedure->client->mother_last_name), $searchTerm) ||
                    $this->searchGrantors($procedure, $searchTerm);

                $vulnerable = $search;
            }
            return $vulnerable;
        })->values();


        $currentPage = Paginator::resolveCurrentPage('page');
        $paginatedExpedient = new LengthAwarePaginator(
            $filteredProcedures->forPage($currentPage, $paginate),
            $filteredProcedures->count(),
            $paginate,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return $this->showList($paginatedExpedient);
    }

    private function searchGrantors($procedure, $searchTerm)
    {
        $found = false;
        if (!is_null($procedure->grantors)) {
            foreach ($procedure->grantors as $grantor) {
                $found = str_contains(
                    (($grantor->name ?? '') . ' ' .
                        ($grantor->father_last_name ?? '') . ' ' .
                        ($grantor->mother_last_name ?? '')),
                    $searchTerm
                );

                if ($found) {
                    break;
                }
            }
        }

        return $found;
    }
}
