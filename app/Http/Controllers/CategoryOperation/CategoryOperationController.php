<?php

namespace App\Http\Controllers\CategoryOperation;

use App\Http\Controllers\ApiController;
use App\Models\CategoryOperation;
use Illuminate\Http\Request;

class CategoryOperationController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('paginate') ?? env('NUMBER_PAGINATE');

        if ($request->has('search') && $request->get('search') !== 'null') {
            $response = CategoryOperation::search($request->get('search'))
                ->with('operation')
                ->orderBy('id', 'desc')->paginate($perPage);
        } else {
            $response = CategoryOperation::with('operation')->orderBy('id', 'desc')->paginate($perPage);
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
        $this->validate($request, CategoryOperation::rules());
        $categoryOperation = CategoryOperation::create($request->all());

        return $this->showOne($categoryOperation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategoryOperation  $categoryOperation
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryOperation $categoryOperation)
    {
        return $this->showOne($categoryOperation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CategoryOperation  $categoryOperation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoryOperation $categoryOperation)
    {
        $this->validate($request, CategoryOperation::rules($categoryOperation->id));
        $categoryOperation->fill($request->all());
        if ($categoryOperation->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $categoryOperation->save();

        return $this->showOne($categoryOperation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategoryOperation  $categoryOperation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoryOperation $categoryOperation)
    {
        $categoryOperation->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
