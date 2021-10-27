<?php

/*
 * CODE
 * Log Controller
*/

namespace App\Http\Controllers\Log;

use Exception;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class LogController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $logs = Log::all();

        return $this->showAll($logs);
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
        $log = Log::create($request->all());

        return $this->showOne($log);
    }

    /**
     * @param Log $log
     *
     * @return JsonResponse
     */
    public function show(Log $log): JsonResponse
    {
        return $this->showOne($log);
    }

    /**
     * @param Request $request
     * @param Log $log
     *
     * @return JsonResponse
     */
    public function update(Request $request, Log $log): JsonResponse
    {
        $log->fill($request->all());
        if ($log->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $log->save();

        return $this->showOne($log);
    }

    /**
     * @param Log $log
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Log $log): JsonResponse
    {
        $log->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
