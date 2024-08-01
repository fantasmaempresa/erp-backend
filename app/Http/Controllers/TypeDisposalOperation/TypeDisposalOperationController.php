<?php

/*
 * OPEN2CODE
 * TypeDisposalOperation Controller
 */

namespace App\Http\Controllers\TypeDisposalOperation;

use App\Http\Controllers\ApiController;
use App\Models\TypeDisposalOperation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @version1
 */
class TypeDisposalOperationController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(TypeDisposalOperation::orderBy('id', 'desc')->paginate($paginate));
    }

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, TypeDisposalOperation::rules());
        $typeDisposalOperation = TypeDisposalOperation::create($request->all());

        return $this->showOne($typeDisposalOperation);
    }

    /**
     * Display the specified resource.
     *
     * @param TypeDisposalOperation $typeDisposalOperation
     *
     * @return JsonResponse
     */
    public function show(TypeDisposalOperation $typeDisposalOperation): JsonResponse
    {
        return $this->showOne($typeDisposalOperation);
    }

    public function update(Request $request, TypeDisposalOperation $typeDisposalOperation): JsonResponse
    {
        $this->validate($request, TypeDisposalOperation::rules());
        $typeDisposalOperation->fill($request->all());
        if($typeDisposalOperation->isClean()){
            return $this->errorResponse('At least one value must change', 422);
        }

        $typeDisposalOperation->save();
        return $this->showOne($typeDisposalOperation);
    }

    public function destroy(TypeDisposalOperation $typeDisposalOperation): JsonResponse
    {
        $typeDisposalOperation->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
