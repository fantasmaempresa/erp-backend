<?php

/*
 * CODE
 * Deduction Controller
*/

namespace App\Http\Controllers\Deduction;

use Exception;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DeductionController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $deductions = Deduction::all();

        return $this->showAll($deductions);
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
        $deduction = Deduction::create($request->all());

        return $this->showOne($deduction);
    }

    /**
     * @param Deduction $deduction
     *
     * @return JsonResponse
     */
    public function show(Deduction $deduction): JsonResponse
    {
        return $this->showOne($deduction);
    }

    /**
     * @param Request $request
     * @param Deduction $deduction
     *
     * @return JsonResponse
     */
    public function update(Request $request, Deduction $deduction): JsonResponse
    {
        $deduction->fill($request->all());
        if ($deduction->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $deduction->save();

        return $this->showOne($deduction);
    }

    /**
     * @param Deduction $deduction
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Deduction $deduction): JsonResponse
    {
        $deduction->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
