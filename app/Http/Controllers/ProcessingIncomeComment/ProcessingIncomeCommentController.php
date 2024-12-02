<?php

namespace App\Http\Controllers\ProcessingIncomeComment;

use App\Http\Controllers\ApiController;
use App\Models\ProcessingIncomeComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessingIncomeCommentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'processing_income_id' => 'required|exists:processing_incomes,id',
        ]);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        
        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(
                ProcessingIncomeComment::search($request->get('search'), $request->get('processing_income_id'))
                ->with('user')->paginate($paginate)
            );
        } else {
            $response = $this->showList(
                ProcessingIncomeComment::where('processing_income_id', $request->get('processing_income_id'))
                ->with('user')
                ->paginate($paginate)
            );
        }

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ProcessingIncomeComment::rules());
        $processingIncomeComment = new ProcessingIncomeComment($request->all());
        $processingIncomeComment->user_id = Auth::id();
        $processingIncomeComment->save();
        // $processingIncomeComment->notify();

        return $this->showOne($processingIncomeComment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProcessingIncomeComment  $processingIncomeComment
     * @return \Illuminate\Http\Response
     */
    public function show(ProcessingIncomeComment $processingIncomeComment)
    {
        $processingIncomeComment->user;
        $processingIncomeComment->processingIncome;

        return $this->showOne($processingIncomeComment);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProcessingIncomeComment  $processingIncomeComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProcessingIncomeComment $processingIncomeComment)
    {
        $this->validate($request, ProcessingIncomeComment::rules());
        $processingIncomeComment->fill($request->all());
        if ($processingIncomeComment->isClean()) {
            return $this->errorResponse('At least one value must change', 422);
        }

        $processingIncomeComment->save();

        return $this->showOne($processingIncomeComment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProcessingIncomeComment  $processingIncomeComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProcessingIncomeComment $processingIncomeComment)
    {
        $processingIncomeComment->delete();
        return $this->successResponse('Processing income comment deleted');
    }
}
