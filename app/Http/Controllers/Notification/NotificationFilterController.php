<?php

/*
 * CODE
 * Notification Controller
*/

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\ApiController;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @access  public
 *
 * @version 1.0
 */
class NotificationFilterController extends ApiController
{
    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function filterUserNotifications(User $user): JsonResponse
    {
        $user->notification;

        return $this->showOne($user);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function filterNoSeenNotification(User $user): JsonResponse
    {
        $notifications = Notification::where('user_id', $user->id)
            ->where('check', Notification::$UNCHECK)
            ->get();

        return $this->showAll($notifications);
    }
}
