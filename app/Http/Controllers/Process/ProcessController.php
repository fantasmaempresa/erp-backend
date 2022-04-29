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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(Process::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Process::paginate($paginate));
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
        $this->validate($request, Process::rules());
        $process = new Process($request->all());
        $validityConfig = $process->verifyConfig($request->get('config'));
        if ($validityConfig) {
            return $this->errorResponse($validityConfig, 409);
        }
        $process->save();

        if ($request->has('phase_process')) {
            foreach ($request->get('phase_process') as $phase) {
                $process->phases()->attach($phase['id']);
            }
        }

        $process->phases;

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
     * @return JsonResponse
     *
     * @throws ValidationException
     *
     */
    public function update(Request $request, Process $process): JsonResponse
    {
        $this->validate($request, Process::rules());
        $validityConfig = $process->verifyConfig($request->get('config'));
        if ($validityConfig) {
            return $this->errorResponse($validityConfig, 409);
        }
        $process->fill($request->all());
        if ($process->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $process->save();

        $ids = [];
        if ($request->has('phase_process')) {
            foreach ($request->get('phase_process') as $phase) {
                $ids = $phase['id'];
            }
        }

        $process->phases()->sync($ids);
        $process->phases;

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
