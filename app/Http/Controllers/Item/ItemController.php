<?php

/*
 * CODE
 * Item Controller
*/

namespace App\Http\Controllers\Item;

use Exception;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ItemController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $items = Item::all();

        return $this->showAll($items);
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
        $item = Item::create($request->all());

        return $this->showOne($item);
    }

    /**
     * @param Item $item
     *
     * @return JsonResponse
     */
    public function show(Item $item): JsonResponse
    {
        return $this->showOne($item);
    }

    /**
     * @param Request $request
     * @param Item $item
     *
     * @return JsonResponse
     */
    public function update(Request $request, Item $item): JsonResponse
    {
        $item->fill($request->all());
        if ($item->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $item->save();

        return $this->showOne($item);
    }

    /**
     * @param Item $item
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
