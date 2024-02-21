<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\RegistrationProcedureData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class RegistrationProcedureDataController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = RegistrationProcedureData::search($request->get('search'))
                ->with('document')
                ->with('user')
                ->orderBy('id', 'DESC')
                ->get();
        } elseif (!empty($request->get('procedure_id')) && $request->get('procedure_id') !== 'null') {
            $response = RegistrationProcedureData::where('procedure_id', $request->get('procedure_id'))
                ->with('document')
                ->with('user')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $response = RegistrationProcedureData::with('document')
                ->with('user')
                ->orderBy('id', 'DESC')
                ->get();
        }

        $response->map(function ($item) {
            $item->url_file = url('storage/app/registration_procedure_data/' . $item->procedure_id . '/' . $item->url_file);
            return $item;        
        });

        $currentPage = Paginator::resolveCurrentPage('page');
        $paginatedResponse = new LengthAwarePaginator(
            $response->forPage($currentPage, $paginate),
            $response->count(),
            $paginate,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return $this->showList($paginatedResponse);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, RegistrationProcedureData::rules());
        $registrationProcedureData = new RegistrationProcedureData($request->all());

        DB::begintransaction();

        try {
            $registrationProcedureData->date = Carbon::parse($registrationProcedureData->date);
            $registrationProcedureData->user_id = Auth::id();
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('registration_procedure_data/' . $registrationProcedureData->procedure_id . '/', $fileName);
            $registrationProcedureData->url_file = $fileName;
            $registrationProcedureData->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('ocurrio un error al almacenar la información ' . $e->getMessage(), 422);
        }

        return $this->showOne($registrationProcedureData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RegistrationProcedureData  $registrationProcedureData
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationProcedureData $registrationProcedureData)
    {
        return $this->showOne($registrationProcedureData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RegistrationProcedureData  $registrationProcedureData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegistrationProcedureData $registrationProcedureData)
    {
        $this->validate($request, RegistrationProcedureData::rules());
        $registrationProcedureData->fill($request->all());
        $registrationProcedureData->date = Carbon::parse($registrationProcedureData->date);
        $registrationProcedureData->user_id = Auth::user();
        $registrationProcedureData->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RegistrationProcedureData  $registrationProcedureData
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegistrationProcedureData $registrationProcedureData)
    {
        $registrationProcedureData->delete();
        return $this->successResponse('delete completed successfully');
    }
}
