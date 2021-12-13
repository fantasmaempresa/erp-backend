<?php

/*
 * CODE
 * Warehouse Controller
*/

namespace App\Http\Controllers\Warehouse;

use Exception;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class WarehouseController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $warehouses = Warehouse::all();

        return $this->showAll($warehouses);
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
        $this->validate($request, Warehouse::rules());
        $warehouse = Warehouse::create($request->all());

        return $this->showOne($warehouse);
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return JsonResponse
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->showOne($warehouse);
    }

    /**
     * @param Request   $request
     * @param Warehouse $warehouse
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $this->validate($request, Warehouse::rules($warehouse->id));
        $warehouse->fill($request->all());
        if ($warehouse->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $warehouse->save();

        return $this->showOne($warehouse);
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
