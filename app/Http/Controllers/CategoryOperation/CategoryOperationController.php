<?php

namespace App\Http\Controllers\CategoryOperation;

use App\Http\Controllers\ApiController;
use App\Models\CategoryOperation;
use App\Models\Procedure;
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
        $operation = new CategoryOperation($request->all());
        $operation->config = 
        array_merge(empty($operation->config) ? [] : $operation->config, 
            ['documents_required' => $request->get('documents')]
        );
        $operation->save();

        return $this->showOne($$operation);
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

        // if ($categoryOperation->isClean()) {
        //     return $this->errorResponse('A different value must be specified to update', 422);
        // }
        $categoryOperation->config = 
        array_merge(empty($categoryOperation->config) ? [] : $categoryOperation->config, 
            ['documents_required' => $request->get('documents')]
        );
        $categoryOperation->save();

        $procedures = Procedure::join('operation_procedure', 'procedures.id', 'operation_procedure.procedure_id')
                ->join('operations', 'operation_procedure.operation_id', 'operations.id')
                ->where('operations.category_operation_id', $categoryOperation->id)
                ->select('procedures.*')
                ->get();
        
        foreach ($procedures as $procedure) {
            $documents = [];
            if(!is_null($categoryOperation->config['documents'])){
                foreach ($categoryOperation->config['documents'] as $document) {
                    $documents[] = $document['id'];
                }

                foreach ($procedure->documents->toArray() as $document) {
                    $documents[] = $document['id'];
                }

                $documents = array_unique($documents);
                $procedure->documents()->sync($documents);
            }
        }

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
