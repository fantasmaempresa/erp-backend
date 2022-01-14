<?php

/*
 * CODE
 * Departure Controller
*/

namespace App\Http\Controllers\Departure;

use Exception;
use App\Models\Departure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DepartureController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $departures = Departure::all();

        return $this->showAll($departures);
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
        $departure = Departure::create($request->all());

        return $this->showOne($departure);
    }

    /**
     * @param Departure $departure
     *
     * @return JsonResponse
     */
    public function show(Departure $departure): JsonResponse
    {
        return $this->showOne($departure);
    }

    /**
     * @param Request $request
     * @param Departure $departure
     *
     * @return JsonResponse
     */
    public function update(Request $request, Departure $departure): JsonResponse
    {
        $departure->fill($request->all());
        if ($departure->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $departure->save();

        return $this->showOne($departure);
    }

    /**
     * @param Departure $departure
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Departure $departure): JsonResponse
    {
        $departure->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
