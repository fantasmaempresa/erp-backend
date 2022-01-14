<?php

/*
 * CODE
 * DepartureDetail Controller
*/

namespace App\Http\Controllers\DepartureDetail;

use Exception;
use App\Models\DepartureDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DepartureDetailController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $departureDetails = DepartureDetail::all();

        return $this->showAll($departureDetails);
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
        $departureDetail = DepartureDetail::create($request->all());

        return $this->showOne($departureDetail);
    }

    /**
     * @param DepartureDetail $departureDetail
     *
     * @return JsonResponse
     */
    public function show(DepartureDetail $departureDetail): JsonResponse
    {
        return $this->showOne($departureDetail);
    }

    /**
     * @param Request $request
     * @param DepartureDetail $departureDetail
     *
     * @return JsonResponse
     */
    public function update(Request $request, DepartureDetail $departureDetail): JsonResponse
    {
        $departureDetail->fill($request->all());
        if ($departureDetail->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $departureDetail->save();

        return $this->showOne($departureDetail);
    }

    /**
     * @param DepartureDetail $departureDetail
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(DepartureDetail $departureDetail): JsonResponse
    {
        $departureDetail->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
