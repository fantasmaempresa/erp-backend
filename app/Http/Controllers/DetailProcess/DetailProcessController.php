<?php

/*
 * CODE
 * DetailProcess Controller
*/

namespace App\Http\Controllers\DetailProcess;

use Exception;
use App\Models\DetailProcess;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DetailProcessController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $detailProcesses = DetailProcess::all();

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
        $detailProcess = DetailProcess::create($request->all());

        return $this->showOne($detailProcess);
    }

    /**
     * @param DetailProcess $detailProcess
     *
     * @return JsonResponse
     */
    public function show(DetailProcess $detailProcess): JsonResponse
    {
        return $this->showOne($detailProcess);
    }

    /**
     * @param Request $request
     * @param DetailProcess $detailProcess
     *
     * @return JsonResponse
     */
    public function update(Request $request, DetailProcess $detailProcess): JsonResponse
    {
        $detailProcess->fill($request->all());
        if ($detailProcess->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $detailProcess->save();

        return $this->showOne($detailProcess);
    }

    /**
     * @param DetailProcess $detailProcess
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(DetailProcess $detailProcess): JsonResponse
    {
        $detailProcess->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
