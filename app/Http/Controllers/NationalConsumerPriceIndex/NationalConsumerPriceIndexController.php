<?php

/*
 * Open2Code
 * NationalConsumerPriceIndex Controller Class
 */
namespace App\Http\Controllers\NationalConsumerPriceIndex;

use App\Http\Controllers\ApiController;
use App\Models\NationalConsumerPriceIndex;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @access  public
 *
 * @version 1.0
 */
class NationalConsumerPriceIndexController extends ApiController
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

        return $this->showList(NationalConsumerPriceIndex::orderBy('id','desc')->paginate($paginate));
    }

    /**
     * Display the specified resource.
     *
     * @param NationalConsumerPriceIndex $nationalConsumerPriceIndex
     *
     * @return JsonResponse
     */
    public function show(NationalConsumerPriceIndex $nationalConsumerPriceIndex): JsonResponse
    {
        return $this->showOne($nationalConsumerPriceIndex);
    }
}
