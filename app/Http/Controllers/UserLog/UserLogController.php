<?php

/*
 * CODE
 * UserLog Controller
*/

namespace App\Http\Controllers\UserLog;

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
class UserLogController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(UserLog::paginate(env('NUMBER_PAGINATE')));
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
        $rules = [
            'date' => 'date',
            'file' => 'string',
            'user_id' => 'int',
        ];

        $this->validate($request, $rules);
        $userLog = UserLog::create($request->all());

        return $this->showOne($userLog);
    }

    /**
     * @param UserLog $userLog
     *
     * @return JsonResponse
     */
    public function show(UserLog $userLog): JsonResponse
    {
        return $this->showOne($userLog);
    }

    /**
     * @param Request $request
     * @param UserLog $userLog
     *
     * @return JsonResponse
     */
    public function update(Request $request, UserLog $userLog): JsonResponse
    {
        $userLog->fill($request->all());
        if ($userLog->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $userLog->save();

        return $this->showOne($userLog);
    }

    /**
     * @param UserLog $userLog
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(UserLog $userLog): JsonResponse
    {
        $userLog->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
