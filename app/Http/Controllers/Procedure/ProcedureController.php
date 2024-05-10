<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Carbon\Carbon;
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
            $response = Procedure::search($request->get('search'))
                ->with('grantors.stake')
                ->with('documents')
                ->with('client')
                ->with('operations')
                ->orderBy('id','desc')
                ->paginate($paginate);
        } else {
            $response = Procedure::with('grantors')
                ->with('documents')
                ->with('client')
                ->with('operations')
                ->orderBy('id','desc')
                ->paginate($paginate);
        }

        return $this->showList($response);
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
            $procedure->user_id = Auth::id();
            $procedure->save();

            //Agregar otrogantes
            foreach ($request->get('grantors') as $grantor) {
                $procedure->grantors()->attach($grantor['id']);
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
        $procedure->grantors;
        $procedure->documents;
        $procedure->operations;

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

        $this->validate($request, Procedure::rules());
        DB::begintransaction();

        try{
            $procedure->fill($request->all());
            $documents = [];
            $grantors = [];
            $operations = [];

            foreach ($request->get('grantors') as $grantor) {
                $grantors[] = $grantor['id'];
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

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $this->errorResponse('error al actualizar --> ' . $e->getMessage(), 409);
        }
        

        $procedure->save();

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
