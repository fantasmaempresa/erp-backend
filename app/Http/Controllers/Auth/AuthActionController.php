<?php
/*
 * CODE
 * Auth Action Controller
*/

namespace App\Http\Controllers\Auth;

use App\Events\SystemActionsEvent;
use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @access  public
 *
 * @version 1.0
 */
class AuthActionController extends ApiController
{
    /**
     * @param Request $request
     * @param User    $user
     *
     * @return JsonResponse
     */
    public function logoutUser(Request $request, User $user): JsonResponse
    {
        $user->AauthAcessToken()->delete();
        $user->online = User::$OFFLINE;
        $user->save();
        event(new SystemActionsEvent($user, User::getActionSystem(User::$LOGOUT)));

        if ($request->has('locked') && $request->get('locked')) {
            $user->locked = User::$LOCKED;
            $user->save();
        }

        return $this->successResponse('user logout!!', 200);
    }
}