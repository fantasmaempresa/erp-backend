<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\RegistrationProcedureData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
                ->with('documents')
                ->orderBy('id', 'DESC')
                ->paginate($paginate);
        }
        elseif (!empty($request->get('procedure_id')) && $request->get('procedure_id') !== 'null') {
            $response = RegistrationProcedureData::where('procedure_id', $request->get('procedure_id'))
                ->with('documents')
                ->orderBy('id', 'DESC')
                ->paginate($paginate);
        } else {
            $response = RegistrationProcedureData::with('documents')
                ->orderBy('id', 'DESC')
                ->paginate($paginate);
        }

        return $this->showList($response);
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
            $registrationProcedureData->user_id = Auth::user();
            $registrationProcedureData->save();
            DB::commit();

        }catch (\Exception $e) {
            
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RegistrationProcedureData  $registrationProcedureData
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationProcedureData $registrationProcedureData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RegistrationProcedureData  $registrationProcedureData
     * @return \Illuminate\Http\Response
     */
    public function edit(RegistrationProcedureData $registrationProcedureData)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RegistrationProcedureData  $registrationProcedureData
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegistrationProcedureData $registrationProcedureData)
    {
        //
    }
}
