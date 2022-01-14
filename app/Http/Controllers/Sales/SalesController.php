<?php

/*
 * CODE
 * Sales Controller
*/

namespace App\Http\Controllers\Sales;

use Exception;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class SalesController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $sales = Sales::all();

        return $this->showAll($sales);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [];

        $this->validate($request, $rules);
        $sales = Sales::create($request->all());

        return $this->showOne($sales);
    }

    /**
     * @param Sales $sales
     *
     * @return JsonResponse
     */
    public function show(Sales $sales): JsonResponse
    {
        return $this->showOne($sales);
    }

    /**
     * @param Request $request
     * @param Sales $sales
     *
     * @return JsonResponse
     */
    public function update(Request $request, Sales $sales): JsonResponse
    {
        $sales->fill($request->all());
        if ($sales->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $sales->save();

        return $this->showOne($sales);
    }

    /**
     * @param Sales $sales
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Sales $sales): JsonResponse
    {
        $sales->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
