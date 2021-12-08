<?php

/*
 * CODE
 * Process Controller
*/

namespace App\Http\Controllers\Process;

use Exception;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProcessController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(Process::paginate(env('NUMBER_PAGINATE')));
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
        $this->validate($request, Process::rules());
        $process = Process::create($request->all());

        return $this->showOne($process);
    }

    /**
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function show(Process $process): JsonResponse
    {
        return $this->showOne($process);
    }

    /**
     * @param Request $request
     * @param Process $process
     *
     *@throws ValidationException
     *
     * @return JsonResponse
     */
    public function update(Request $request, Process $process): JsonResponse
    {
        $this->validate($request, Process::rules());
        $process->fill($request->all());
        if ($process->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $process->save();

        return $this->showOne($process);
    }

    /**
     * @param Process $process
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Process $process): JsonResponse
    {
        $process->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
