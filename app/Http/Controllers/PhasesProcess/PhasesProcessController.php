<?php

/*
 * CODE
 * PhasesProcess Controller
*/

namespace App\Http\Controllers\PhasesProcess;

use Exception;
use App\Models\PhasesProcess;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class PhasesProcessController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $phasesProcesses = PhasesProcess::all();

        return $this->showAll($phasesProcesses);
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
        $phasesProcess = PhasesProcess::create($request->all());

        return $this->showOne($phasesProcess);
    }

    /**
     * @param PhasesProcess $phasesProcess
     *
     * @return JsonResponse
     */
    public function show(PhasesProcess $phasesProcess): JsonResponse
    {
        return $this->showOne($phasesProcess);
    }

    /**
     * @param Request $request
     * @param PhasesProcess $phasesProcess
     *
     * @return JsonResponse
     */
    public function update(Request $request, PhasesProcess $phasesProcess): JsonResponse
    {
        $phasesProcess->fill($request->all());
        if ($phasesProcess->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $phasesProcess->save();

        return $this->showOne($phasesProcess);
    }

    /**
     * @param PhasesProcess $phasesProcess
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(PhasesProcess $phasesProcess): JsonResponse
    {
        $phasesProcess->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
