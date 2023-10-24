<?php

/*
 * OPEN2CODE
 */
namespace App\Http\Controllers\Appendant;

use App\Http\Controllers\ApiController;
use App\Models\Appendant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @version1
 */
class AppendantController extends ApiController
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

        return $this->showList(Appendant::paginate($paginate));
    }

    /**
     * Display the specified resource.
     *
     * @param Appendant $appendant
     *
     * @return JsonResponse
     */
    public function show(Appendant $appendant): JsonResponse
    {
        return $this->showOne($appendant);
    }
}
