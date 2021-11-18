<?php

/*
 * CODE
 * Inventory Controller
*/

namespace App\Http\Controllers\Inventory;

use Exception;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class InventoryController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $inventories = Inventory::all();

        return $this->showAll($inventories);
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
        $inventory = Inventory::create($request->all());

        return $this->showOne($inventory);
    }

    /**
     * @param Inventory $inventory
     *
     * @return JsonResponse
     */
    public function show(Inventory $inventory): JsonResponse
    {
        return $this->showOne($inventory);
    }

    /**
     * @param Request $request
     * @param Inventory $inventory
     *
     * @return JsonResponse
     */
    public function update(Request $request, Inventory $inventory): JsonResponse
    {
        $inventory->fill($request->all());
        if ($inventory->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $inventory->save();

        return $this->showOne($inventory);
    }

    /**
     * @param Inventory $inventory
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Inventory $inventory): JsonResponse
    {
        $inventory->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
