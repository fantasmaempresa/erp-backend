<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Folio;
use App\Models\Procedure;
use App\Models\Role;
use App\Models\Stake;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcedureController extends ApiController

{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $query = Procedure::search($request->get('search'))
                ->with('grantors.grantorProcedure.stake')
                ->with('user')
                ->with('documents')
                ->with('client')
                ->with('operations')
                ->with('comments')
                ->with('registrationProcedureData')
                ->with('staff')
                ->with('folio')
                ->with('processingIncome');
        } else if (!empty($request->get('superFilter'))) {
            $query = Procedure::advanceFilter(json_decode($request->get('superFilter')))
                ->with('grantors.grantorProcedure.stake')
                ->with('user')
                ->with('documents')
                ->with('client')
                ->with('operations')
                ->with('comments')
                ->with('staff')
                ->with('folio')
                ->with('registrationProcedureData')
                ->with('processingIncome');
        } else {
            $query = Procedure::with('grantors.grantorProcedure.stake')
                ->with('user')
                ->with('documents')
                ->with('client')
                ->with('operations')
                ->with('comments')
                ->with('staff')
                ->with('folio')
                ->with('registrationProcedureData')
                ->with('processingIncome');
        }
        $procedures = $query->orderby('name', 'desc')->paginate($paginate);

        return $this->showList($procedures);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {

        $this->validate($request, Procedure::rules());
        $procedure = new Procedure($request->all());

        DB::begintransaction();

        try {
            $procedure->date = Carbon::parse($procedure->date);
            $procedure->date_proceedings = Carbon::parse($procedure->date_proceedings);
            $procedure->user_id = Auth::id();

            $procedure->save();

            if (!empty($request->get('folio_id'))) {
                $folio = Folio::findOrFail($request->get('folio_id'));
                if ($folio->procedure_id == null) {
                    $folio->procedure_id = $procedure->id;
                    $folio->save();
                }
                $procedure->status = Procedure::ACCEPTED;
            }

            //Agregar otrogantes
            if (!empty($request->get('grantors'))) {
                foreach ($request->get('grantors') as $grantor) {
                    $procedure->grantors()->attach($grantor['grantor_id'], ['stake_id' => $grantor['stake_id']]);
                }
            }

            foreach ($request->get('documents') as $grantor) {
                $procedure->documents()->attach($grantor['id']);
            }

            foreach ($request->get('operations') as $operation) {
                $procedure->operations()->attach($operation['id']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('error al almacenar información --> ' . $e->getMessage(), 409);
        }


        $procedure->grantors;
        $procedure->documents;
        $procedure->operations;

        return $this->showOne($procedure);
    }

    /**
     * Display the specified resource.
     *
     * @param Procedure $procedure
     *
     * @return JsonResponse
     */
    public function show(Procedure $procedure)
    {
        foreach ($procedure->grantors as $grantor) {
            $grantor->stake_id = $grantor->pivot->stake_id;
            $grantor->grantor_id = $grantor->id;
        }
        $procedure->documents;
        $procedure->operations;
        $procedure->load('operations.categoryOperation');
        $procedure->load('folio.book');

        return $this->showOne($procedure);
    }


    /**
     * @param Request $request
     * @param Procedure $procedure
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Procedure $procedure): JsonResponse
    {

        $this->validate($request, Procedure::rules($procedure->id));

        $user = Auth::user();

        if ($procedure->user_id != 6 && $user->role_id != Role::$ADMIN && $user->id != $procedure->user_id) {
            return $this->errorResponse('No puede editar este proceso, por favor informar al propietario del registro', 422);
        }

        DB::begintransaction();

        try {
            $procedure->fill($request->all());
            $procedure->date = Carbon::parse($procedure->date);
            $procedure->date_proceedings = Carbon::parse($procedure->date_proceedings);
            $documents = [];
            $grantors = [];
            $operations = [];

            if (!empty($request->get('folio_id'))) {
                $folio = Folio::findOrFail($request->get('folio_id'));
                $folio->procedure_id = $procedure->id;
                $folio->save();
            }

            if (!empty($request->get('grantors'))) {
                foreach ($request->get('grantors') as $grantor) {
                    $grantors[$grantor['grantor_id']] = ['stake_id' => $grantor['stake_id']];
                }
            }

            foreach ($request->get('documents') as $document) {
                $documents[] = $document['id'];
            }

            foreach ($request->get('operations') as $operation) {
                $operations[] = $operation['id'];
            }

            $procedure->grantors()->sync($grantors);
            $procedure->documents()->sync($documents);
            $procedure->operations()->sync($operations);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('error al actualizar --> ' . $e->getMessage(), 409);
        }


        $procedure->save();
        DB::commit();

        $procedure->grantors;
        $procedure->documents;
        $procedure->operations;

        return $this->showOne($procedure);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Procedure $procedure
     *
     * @return JsonResponse
     */
    public function destroy(Procedure $procedure)
    {
        $procedure->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}
