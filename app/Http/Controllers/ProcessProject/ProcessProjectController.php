<?php

/*
 * CODE
 * ProcessProject Controller
*/

namespace App\Http\Controllers\ProcessProject;

use Exception;
use App\Models\ProcessProject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProcessProjectController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $processProjects = ProcessProject::orderBy('id','desc')->all();

        return $this->showAll($processProjects);
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
        $processProject = ProcessProject::create($request->all());

        return $this->showOne($processProject);
    }

    /**
     * @param ProcessProject $processProject
     *
     * @return JsonResponse
     */
    public function show(ProcessProject $processProject): JsonResponse
    {
        return $this->showOne($processProject);
    }

    /**
     * @param Request $request
     * @param ProcessProject $processProject
     *
     * @return JsonResponse
     */
    public function update(Request $request, ProcessProject $processProject): JsonResponse
    {
        $processProject->fill($request->all());
        if ($processProject->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $processProject->save();

        return $this->showOne($processProject);
    }

    /**
     * @param ProcessProject $processProject
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(ProcessProject $processProject): JsonResponse
    {
        $processProject->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
