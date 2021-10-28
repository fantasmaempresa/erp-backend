<?php

/*
 * CODE
 * ProjectStaff Controller
*/

namespace App\Http\Controllers\ProjectStaff;

use Exception;
use App\Models\ProjectStaff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectStaffController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $projectStaffs = ProjectStaff::all();

        return $this->showAll($projectStaffs);
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
        $projectStaff = ProjectStaff::create($request->all());

        return $this->showOne($projectStaff);
    }

    /**
     * @param ProjectStaff $projectStaff
     *
     * @return JsonResponse
     */
    public function show(ProjectStaff $projectStaff): JsonResponse
    {
        return $this->showOne($projectStaff);
    }

    /**
     * @param Request $request
     * @param ProjectStaff $projectStaff
     *
     * @return JsonResponse
     */
    public function update(Request $request, ProjectStaff $projectStaff): JsonResponse
    {
        $projectStaff->fill($request->all());
        if ($projectStaff->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $projectStaff->save();

        return $this->showOne($projectStaff);
    }

    /**
     * @param ProjectStaff $projectStaff
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(ProjectStaff $projectStaff): JsonResponse
    {
        $projectStaff->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
