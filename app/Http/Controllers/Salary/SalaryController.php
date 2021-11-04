<?php

/*
 * CODE
 * Salary Controller
*/

namespace App\Http\Controllers\Salary;

use Exception;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class SalaryController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $salaries = Salary::all();

        return $this->showAll($salaries);
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
        $salary = Salary::create($request->all());

        return $this->showOne($salary);
    }

    /**
     * @param Salary $salary
     *
     * @return JsonResponse
     */
    public function show(Salary $salary): JsonResponse
    {
        return $this->showOne($salary);
    }

    /**
     * @param Request $request
     * @param Salary  $salary
     *
     * @return JsonResponse
     */
    public function update(Request $request, Salary $salary): JsonResponse
    {
        $salary->fill($request->all());
        if ($salary->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $salary->save();

        return $this->showOne($salary);
    }

    /**
     * @param Salary $salary
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Salary $salary): JsonResponse
    {
        $salary->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
