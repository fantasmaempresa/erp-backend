<?php

/*
 * CODE
 * ExtraHour Controller
*/

namespace App\Http\Controllers\ExtraHour;

use Exception;
use App\Models\ExtraHour;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ExtraHourController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $extraHours = ExtraHour::all();

        return $this->showAll($extraHours);
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
        $rules = [
            'days'         => 'required',
            'type'         => 'required',
            'hours'        => 'required',
            'amount'       => 'required',
            'tax_datum_id' => 'required',
        ];

        $this->validate($request, $rules);
        $extraHour = ExtraHour::create($request->all());

        return $this->showOne($extraHour);
    }

    /**
     * @param ExtraHour $extraHour
     *
     * @return JsonResponse
     */
    public function show(ExtraHour $extraHour): JsonResponse
    {
        return $this->showOne($extraHour);
    }

    /**
     * @param Request   $request
     * @param ExtraHour $extraHour
     *
     * @return JsonResponse
     */
    public function update(Request $request, ExtraHour $extraHour): JsonResponse
    {
        $extraHour->fill($request->all());
        if ($extraHour->isClean()) {
            return $this->errorResponse(
                'A different value must be specified to update',
                422
            );
        }

        $extraHour->save();

        return $this->showOne($extraHour);
    }

    /**
     * @param ExtraHour $extraHour
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(ExtraHour $extraHour): JsonResponse
    {
        $extraHour->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
