<?php

/*
 * CODE
 * UserLog Controller
*/

namespace App\Http\Controllers\Log;

use Exception;
use App\Models\UserLog;
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
        $logs = UserLog::all();

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
        $log = UserLog::create($request->all());

        return $this->showOne($log);
    }

    /**
     * @param UserLog $log
     *
     * @return JsonResponse
     */
    public function show(UserLog $log): JsonResponse
    {
        return $this->showOne($log);
    }

    /**
     * @param Request $request
     * @param UserLog $log
     *
     * @return JsonResponse
     */
    public function update(Request $request, UserLog $log): JsonResponse
    {
        $log->fill($request->all());
        if ($log->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $log->save();

        return $this->showOne($log);
    }

    /**
     * @param UserLog $log
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(UserLog $log): JsonResponse
    {
        $log->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
