<?php

namespace App\Http\Controllers\VulnerableOperation;

use App\Http\Controllers\ApiController;
use App\Models\VulnerableOperation;
use Illuminate\Http\Request;

class VulnerableOperationController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $vulnerableOperations = VulnerableOperation::search($request->get('search'))->with(['procedure'])->paginate($paginate);
        } else if ($request->has('procedure_id')) {
            $vulnerableOperations = VulnerableOperation::where('procedure_id', $request->get('procedure_id'))->with(['procedure'])->paginate($paginate);
        }

        return $this->showList($vulnerableOperations);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, VulnerableOperation::rules());
        $vulnerableOperation = VulnerableOperation::create($request->all());

        return $this->showOne($vulnerableOperation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VulnerableOperation  $vulnerableOperation
     * @return \Illuminate\Http\Response
     */
    public function show(VulnerableOperation $vulnerableOperation)
    {
        $vulnerableOperation->procedure;

        return $this->showOne($vulnerableOperation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VulnerableOperation  $vulnerableOperation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VulnerableOperation $vulnerableOperation)
    {
        $this->validate($request, VulnerableOperation::rules());
        $vulnerableOperation->fill($request->all());
        if ($vulnerableOperation->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $vulnerableOperation->save();
        return $this->showOne($vulnerableOperation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VulnerableOperation  $vulnerableOperation
     * @return \Illuminate\Http\Response
     */
    public function destroy(VulnerableOperation $vulnerableOperation)
    {
        $vulnerableOperation->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
