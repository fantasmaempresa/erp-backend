<?php

/*
 * Open2Code
 * Rate Controller Class
 */
namespace App\Http\Controllers\Rate;

use App\Http\Controllers\ApiController;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @access  public
 *
 * @version 1.0
 */
class RateController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(Rate::orderBy('id','desc')->paginate($paginate));
    }

    /**
     * Display the specified resource.
     *
     * @param  Rate $rate
     *
     * @return JsonResponse
     */
    public function show(Rate $rate): JsonResponse
    {
        return $this->showOne($rate);
    }
}
