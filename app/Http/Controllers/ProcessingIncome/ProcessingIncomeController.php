<?php

namespace App\Http\Controllers\ProcessingIncome;

use App\Http\Controllers\ApiController;
use App\Models\Document;
use App\Models\ProcessingIncome;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessingIncomeController extends ApiController
{
    
    /**
     * A description of the entire PHP function.
     *
     * @param Request $request description
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function index(Request $request)
    {
        $perPage = $request->get('paginate') ?? env('NUMBER_PAGINATE');

        return $this->showList(ProcessingIncome::with(['procedure', 'operation', 'staff', 'place', 'user', 'documents', 'processingIncomeComments'])
            ->orderBy('id', 'desc')
            ->paginate($perPage));
    }

    
    /**
     * Store a new processing income.
     *
     * @param Request $request The request data
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, ProcessingIncome::rules());
        $processingIncome = new ProcessingIncome($request->all());
        $processingIncome->user_id = Auth::id();
        $processingIncome->save();

        if($request->has('documents')) {
            foreach($request->get('documents') as $document) {
                $processingIncome->documents()->attach(Document::find($document['id']));
            }
        }

        return $this->showOne($processingIncome);
    }

    
    public function show(ProcessingIncome $processingIncome): JsonResponse
    {
        $processingIncome->procedure;
        $processingIncome->operation;
        $processingIncome->staff;
        $processingIncome->place;
        $processingIncome->user;
        $processingIncome->documents;
        $processingIncome->processingIncomeComments;

        return $this->showOne($processingIncome);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProcessingIncome  $processingIncome
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProcessingIncome $processingIncome): JsonResponse
    {
        $this->validate($request, ProcessingIncome::rules());
        $processingIncome->fill($request->all());
        $processingIncome->save();

        if($request->has('documents')) {
            $processingIncome->documents()->detach();
            foreach($request->get('documents') as $document) {
                $processingIncome->documents()->attach(Document::find($document['id']));
            }
        }
        return $this->showOne($processingIncome);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProcessingIncome  $processingIncome
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProcessingIncome $processingIncome)
    {
        $processingIncome->processingIncomeComments()->delete();
        $processingIncome->documents()->detach();
        $processingIncome->delete();

        return $this->successResponse('Processing income deleted');
    }
}
