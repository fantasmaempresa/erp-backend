<?php

/*
 * CODE
 * Project Controller
*/

namespace App\Http\Controllers\Project;

use Exception;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(Project::paginate(env('NUMBER_PAGINATE')));
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
        $this->validate($request, Project::rules());
        $project = Project::create($request->all());

        return $this->showOne($project);
    }

    /**
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        return $this->showOne($project);
    }

    /**
     * @param Request $request
     * @param Project $project
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $this->validate($request, Project::rules());
        $project->fill($request->all());
        if ($project->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $project->save();

        return $this->showOne($project);
    }

    /**
     * @param Project $project
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
