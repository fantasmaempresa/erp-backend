<?php

/*
 * CODE
 * DetailProject Controller
*/

namespace App\Http\Controllers\DetailProject;

use Exception;
use App\Models\DetailProject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DetailProjectController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $detailProcesses = DetailProject::all();

        return $this->showAll($detailProcesses);
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
        $detailProcess = DetailProject::create($request->all());

        return $this->showOne($detailProcess);
    }

    /**
     * @param DetailProject $detailProcess
     *
     * @return JsonResponse
     */
    public function show(DetailProject $detailProcess): JsonResponse
    {
        return $this->showOne($detailProcess);
    }

    /**
     * @param Request $request
     * @param DetailProject $detailProcess
     *
     * @return JsonResponse
     */
    public function update(Request $request, DetailProject $detailProcess): JsonResponse
    {
        $detailProcess->fill($request->all());
        if ($detailProcess->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $detailProcess->save();

        return $this->showOne($detailProcess);
    }

    /**
     * @param DetailProject $detailProcess
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(DetailProject $detailProcess): JsonResponse
    {
        $detailProcess->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
