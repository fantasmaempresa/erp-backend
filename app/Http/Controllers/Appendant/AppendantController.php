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

        return $this->showList(Appendant::orderBy('id','desc')->paginate($paginate));
    }

    /**
     * A description of the entire PHP function.
     *
     * @param Request $request description
     * @param Appendant $appendant description
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function update(Request $request, Appendant $appendant)
    {
        $this->validate($request, Appendant::rules());
        $appendant->fill($request->all());
        if($appendant->isClean()){
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $appendant->save();
        return $this->showOne($appendant);
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
