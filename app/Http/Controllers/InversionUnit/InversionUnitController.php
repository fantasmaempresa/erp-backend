<?php

/*
 * OPEN2CODE
 */
namespace App\Http\Controllers\InversionUnit;

use App\Http\Controllers\ApiController;
use App\Models\InversionUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @version1
 */
class InversionUnitController extends ApiController
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

        return $this->showList(InversionUnit::orderBy('id','desc')->paginate($paginate));
    }

    public function store(Request $request): JsonResponse
    {
        $this->validate($request, InversionUnit::rules());
        $inversionUnit = InversionUnit::create($request->all());

        return $this->showOne($inversionUnit);
    }

    /**
     * Display the specified resource.
     *
     * @param InversionUnit $inversionUnit
     *
     * @return JsonResponse
     */
    public function show(InversionUnit $inversionUnit): JsonResponse
    {
        return $this->showOne($inversionUnit);
    }

    public function update(Request $request, InversionUnit $inversionUnit): JsonResponse
    {
        $this->validate($request, InversionUnit::rules($inversionUnit->id));
        $inversionUnit->fill($request->all());
        if ($inversionUnit->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $inversionUnit->save();

        return $this->showOne($inversionUnit);
    }

    public function destroy(InversionUnit $inversionUnit): JsonResponse
    {
        $inversionUnit->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
