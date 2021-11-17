<?php

/*
 * CODE
 * Perception Controller
*/

namespace App\Http\Controllers\Perception;

use Exception;
use App\Models\Perception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class PerceptionController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $perceptions = Perception::all();

        return $this->showAll($perceptions);
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
            'key'               => 'required',
            'concept'           => 'required',
            'aggravated_amount' => 'required',
            'exempt_amount'     => 'required',
        ];

        $this->validate($request, $rules);
        $perception = Perception::create($request->all());

        return $this->showOne($perception);
    }

    /**
     * @param Perception $perception
     *
     * @return JsonResponse
     */
    public function show(Perception $perception): JsonResponse
    {
        return $this->showOne($perception);
    }

    /**
     * @param Request    $request
     * @param Perception $perception
     *
     * @return JsonResponse
     */
    public function update(Request $request, Perception $perception): JsonResponse
    {
        $perception->fill($request->all());
        if ($perception->isClean()) {
            return $this->errorResponse(
                'A different value must be specified to update',
                422
            );
        }

        $perception->save();

        return $this->showOne($perception);
    }

    /**
     * @param Perception $perception
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Perception $perception): JsonResponse
    {
        $perception->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
