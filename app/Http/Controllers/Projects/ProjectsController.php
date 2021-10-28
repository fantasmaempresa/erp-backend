<?php

/*
 * CODE
 * Projects Controller
*/

namespace App\Http\Controllers\Projects;

use Exception;
use App\Models\Projects;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectsController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $projects = Projects::all();

        return $this->showAll($projects);
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
        $projects = Projects::create($request->all());

        return $this->showOne($projects);
    }

    /**
     * @param Projects $projects
     *
     * @return JsonResponse
     */
    public function show(Projects $projects): JsonResponse
    {
        return $this->showOne($projects);
    }

    /**
     * @param Request $request
     * @param Projects $projects
     *
     * @return JsonResponse
     */
    public function update(Request $request, Projects $projects): JsonResponse
    {
        $projects->fill($request->all());
        if ($projects->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $projects->save();

        return $this->showOne($projects);
    }

    /**
     * @param Projects $projects
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Projects $projects): JsonResponse
    {
        $projects->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
