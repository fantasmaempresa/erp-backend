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

        return $this->showList(InversionUnit::paginate($paginate));
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
}
