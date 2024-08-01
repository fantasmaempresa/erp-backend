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
     * Creates a new NationalConsumerPriceIndex from the given request data.
     *
     * @param Request $request The request data to create the NationalConsumerPriceIndex
     * @throws Some_Exception_Class description of exception
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, NationalConsumerPriceIndex::rules());
        $nationalConsumerPriceIndex = NationalConsumerPriceIndex::create($request->all());

        return $this->showOne($nationalConsumerPriceIndex);
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


    /**
     * Update a national consumer price index.
     *
     * @param Request $request The request data
     * @param NationalConsumerPriceIndex $nationalConsumerPriceIndex The national consumer price index to update
     * @return JsonResponse
     */
    public function update(Request $request, NationalConsumerPriceIndex $nationalConsumerPriceIndex): JsonResponse
    {
        $this->validate($request, NationalConsumerPriceIndex::rules());
        $nationalConsumerPriceIndex->fill($request->all());
        if ($nationalConsumerPriceIndex->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $nationalConsumerPriceIndex->save();

        return $this->showOne($nationalConsumerPriceIndex);
    }

    public function destroy(NationalConsumerPriceIndex $nationalConsumerPriceIndex): JsonResponse
    {
        $nationalConsumerPriceIndex->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
