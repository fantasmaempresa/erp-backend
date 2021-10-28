<?php

/*
 * CODE
 * DetailProjectProcessProject Controller
*/

namespace App\Http\Controllers\DetailProjectProcessProject;

use Exception;
use App\Models\DetailProjectProcessProject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DetailProjectProcessProjectController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $detailProjectProcessProjects = DetailProjectProcessProject::all();

        return $this->showAll($detailProjectProcessProjects);
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
        $detailProjectProcessProject = DetailProjectProcessProject::create($request->all());

        return $this->showOne($detailProjectProcessProject);
    }

    /**
     * @param DetailProjectProcessProject $detailProjectProcessProject
     *
     * @return JsonResponse
     */
    public function show(DetailProjectProcessProject $detailProjectProcessProject): JsonResponse
    {
        return $this->showOne($detailProjectProcessProject);
    }

    /**
     * @param Request $request
     * @param DetailProjectProcessProject $detailProjectProcessProject
     *
     * @return JsonResponse
     */
    public function update(Request $request, DetailProjectProcessProject $detailProjectProcessProject): JsonResponse
    {
        $detailProjectProcessProject->fill($request->all());
        if ($detailProjectProcessProject->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $detailProjectProcessProject->save();

        return $this->showOne($detailProjectProcessProject);
    }

    /**
     * @param DetailProjectProcessProject $detailProjectProcessProject
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(DetailProjectProcessProject $detailProjectProcessProject): JsonResponse
    {
        $detailProjectProcessProject->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
