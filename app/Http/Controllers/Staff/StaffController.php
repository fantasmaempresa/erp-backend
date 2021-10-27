<?php

/*
 * CODE
 * Staff Controller
*/

namespace App\Http\Controllers\Staff;

use Exception;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class StaffController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $staff = Staff::all();

        return $this->showAll($staff);
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
        $staff = Staff::create($request->all());

        return $this->showOne($staff);
    }

    /**
     * @param Staff $staff
     *
     * @return JsonResponse
     */
    public function show(Staff $staff): JsonResponse
    {
        return $this->showOne($staff);
    }

    /**
     * @param Request $request
     * @param Staff $staff
     *
     * @return JsonResponse
     */
    public function update(Request $request, Staff $staff): JsonResponse
    {
        $staff->fill($request->all());
        if ($staff->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $staff->save();

        return $this->showOne($staff);
    }

    /**
     * @param Staff $staff
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Staff $staff): JsonResponse
    {
        $staff->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
