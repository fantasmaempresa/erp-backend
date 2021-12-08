<?php
/*
 * CODE
 * User Controller
*/

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * @access  public
 *
 * @version 1.0
 */
class UserController extends ApiController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(User::with('role')->paginate(env('NUMBER_PAGINATE')));
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
        $this->validate($request, User::rules());
        $user = User::create($request->all());
        $user->password = bcrypt($user->password);
        $user->save();

        return $this->showOne($user);
    }

    /**
     * Display the specified resource.*
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return JsonResponse
     *
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->validate($request, User::rules($user->id));
        $user->fill($request->all());
        if ($user->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $user->password = empty($request->has('password'))
                        ? $user->password
                        : bcrypt($request->has('password'));
        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
