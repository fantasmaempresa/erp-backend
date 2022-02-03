<?php

/*
 * CODE
 * Notification Controller
*/

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @access  public
 *
 * @version 1.0
 */
class NotificationFilterController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getLastUserNotifications(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        $user = User::findOrFail(Auth::id());

        // phpcs:ignore
        return $this->showList(Notification::orWhere('user_id', $user->id)->orWhere('role_id', $user->role_id)->orderBy('id', 'DESC')->paginate($paginate));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getUncheckUserNotifications(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        $user = User::findOrFail(Auth::id());

        // phpcs:ignore
        return $this->showList(Notification::orWhere('user_id', $user->id)->orWhere('role_id', $user->role_id)->where('check', Notification::$UNCHECK)->orderBy('id', 'DESC')->paginate($paginate));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getCheckUserNotifications(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        $user = User::findOrFail(Auth::id());
        // phpcs:ignore
        return $this->showList(Notification::orWhere('user_id', $user->id)->orWhere('role_id', $user->role_id)->where('check', Notification::$CHECK)->orderBy('id', 'DESC')->paginate($paginate));
    }
}
