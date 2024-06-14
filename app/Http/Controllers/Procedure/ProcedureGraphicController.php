<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcedureGraphicController extends ApiController
{
    public function registredProcedures()
    {
        $user = User::findOrFail(Auth::id());
        if ($user->id == Role::$ADMIN) {
            $procedures = Procedure::join('users', 'procedures.user_id', '=', 'users.id')
                ->select('users.name as user_name', DB::raw('count(procedures.id) as procedure_count'))
                ->groupBy('users.name')
                ->get();

            $lables = $procedures->pluck('user_name')->toArray();
            $data = $procedures->pluck('procedure_count')->toArray();

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Usuarios',
                        'data' => $data,
                    ]
                ]
            ];
        } else {
            $procedures = Procedure::select(DB::raw('DATE(created_at) as date'), DB::raw('count(id) as procedure_count'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'), 'desc')
                ->get();

            $lables = $procedures->pluck('date')->toArray();
            $data = $procedures->pluck('procedure_count')->toArray();

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Trámites',
                        'data' => $data,
                    ]
                ]
            ];
        }

        return $this->showList($dataSet);
    }

    public function proceduresWithoutData()
    {
        $user = User::findOrFail(Auth::id());
        if ($user->id == Role::$ADMIN) {
            $procedures = Procedure::leftJoin('registration_procedure_data', 'procedures.id', '=', 'registration_procedure_data.procedure_id')
                ->join('users', 'procedures.user_id', '=', 'users.id')
                ->where('registration_procedure_data.procedure_id', null)
                ->select('users.name as user_name', DB::raw('count(procedures.id) as procedure_count'))
                ->groupBy('users.name')
                ->get();

            $lables = $procedures->pluck('user_name')->toArray();
            $data = $procedures->pluck('procedure_count')->toArray();

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Usuarios',
                        'data' => $data,
                    ]
                ]
            ];
        } else {
            $proceduresW = Procedure::leftJoin('registration_procedure_data', 'procedures.id', '=', 'registration_procedure_data.procedure_id')
                ->where('registration_procedure_data.procedure_id', null)
                ->where('procedures.user_id', $user->id)
                ->select('procedures.id')
                ->groupBy('procedures.id')
                ->get();

            $proceduresT = Procedure::join('registration_procedure_data', 'procedures.id', '=', 'registration_procedure_data.procedure_id')
                ->where('procedures.user_id', $user->id)
                ->select('procedures.id')
                ->groupBy('procedures.id')
                ->get();

            $lables = ['Tramites sin datos', 'Tramites con datos'];
            $data = [$proceduresW->count(), $proceduresT->count()];

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Trámites',
                        'data' => $data,
                    ]
                ]
            ];
        }

        return $this->showList($dataSet);
    }

    public function proceduresWithoutShape()
    {
        $user = User::findOrFail(Auth::id());
        if ($user->id == Role::$ADMIN) {
            $procedures = Procedure::leftJoin('shapes', 'procedures.id', '=', 'shapes.procedure_id')
                ->join('users', 'procedures.user_id', '=', 'users.id')
                ->where('shapes.procedure_id', null)
                ->select('users.name as user_name', DB::raw('count(procedures.id) as procedure_count'))
                ->groupBy('users.name')
                ->get();

            $lables = $procedures->pluck('user_name')->toArray();
            $data = $procedures->pluck('procedure_count')->toArray();

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Usuarios',
                        'data' => $data,
                    ]
                ]
            ];
        } else {
            $proceduresW = Procedure::leftJoin('shapes', 'procedures.id', '=', 'shapes.procedure_id')
                ->where('shapes.procedure_id', null)
                ->where('procedures.user_id', $user->id)
                ->select('procedures.id')
                ->groupBy('procedures.id')
                ->get();

            $proceduresT = Procedure::join('shapes', 'procedures.id', '=', 'shapes.procedure_id')
                ->where('procedures.user_id', $user->id)
                ->select('procedures.id')
                ->groupBy('procedures.id')
                ->get();

            $lables = ['Tramites sin formas', 'Tramites con formas'];
            $data = [$proceduresW->count(), $proceduresT->count()];

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Trámites',
                        'data' => $data,
                    ]
                ]
            ];
        }

        return $this->showList($dataSet);
    }

    public function procedureWithoutDocument()
    {
        $user = User::findOrFail(Auth::id());
        if ($user->id == Role::$ADMIN) {
            $procedures = Procedure::leftJoin('document_procedure', 'procedures.id', '=', 'document_procedure.procedure_id')
                ->join('users', 'procedures.user_id', '=', 'users.id')
                ->where('document_procedure.procedure_id', null)
                ->orWhere('document_procedure.file', '')
                ->select('users.name as user_name', DB::raw('count(procedures.id) as procedure_count'))
                ->groupBy('users.name')
                ->get();

            $lables = $procedures->pluck('user_name')->toArray();
            $data = $procedures->pluck('procedure_count')->toArray();

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Usuarios',
                        'data' => $data,
                    ]
                ]
            ];
        } else {
            $proceduresW = Procedure::leftJoin('document_procedure', 'procedures.id', '=', 'document_procedure.procedure_id')
                ->where(function ($query) {
                    $query->where('document_procedure.procedure_id', null)
                        ->orWhere('document_procedure.file', '');
                })
                ->where('procedures.user_id', $user->id)
                ->select('procedures.id')
                ->groupBy('procedures.id')
                ->toSql();

            dd($proceduresW);

            $proceduresT = Procedure::join('document_procedure', 'procedures.id', '=', 'document_procedure.procedure_id')
                ->where('document_procedure.file', '!=', '')
                ->where('procedures.user_id', $user->id)
                ->select('procedures.id')
                ->groupBy('procedures.id')
                ->get();


            $lables = ['Tramites sin documentos', 'Tramites con documentos'];
            $data = [$proceduresW->count(), $proceduresT->count()];

            $dataSet = [
                'labels' => $lables,
                'datasets' => [
                    [
                        'label' => 'Trámites',
                        'data' => $data,
                    ]
                ]
            ];
        }

        return $this->showList($dataSet);
    }
}
