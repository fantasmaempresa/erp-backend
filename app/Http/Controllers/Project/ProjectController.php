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
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectController extends ApiController
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(Project::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Project::paginate($paginate));
        }

        return $response;
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
        $project = new Project($request->all());
        $validityConfig = $project->verifyConfig($request->get('config'));
        if ($validityConfig) {
            return $this->errorResponse($validityConfig, 409);
        }
        // phpcs:ignore
        $project->user_id = Auth::id();
        $project->save();

        $processAndUsers = $project->getUsersAndProcess($request->get('config'));

        foreach ($processAndUsers['process'] as $process) {
            $project->process()->attach($process);
        }

        foreach ($processAndUsers['users'] as $user) {
            $project->users()->attach($user);
        }

        $project->process;
        $project->users;
        $project->user;
        $project->client;
        $project->projectQuote;

        return $this->showOne($project);
    }

    /**
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        $project->process;
        $project->users;
        $project->user;
        $project->client;
        $project->projectQuote;

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

        $ids = [];
        if ($request->has('processes')) {
            foreach ($request->get('processes') as $process) {
                $ids[] = $process['id'];
            }
        }

        $project->process()->sync($ids);
        $project->process;

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
