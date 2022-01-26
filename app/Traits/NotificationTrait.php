<?php
/*
 * Copyright CODE (2021)
 * Trait Notification
 */

namespace App\Traits;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Notifications\QuoteNotification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification as TypeNotification;
use Illuminate\Support\Facades\Notification as ViaNotification;

/**
 * @access  public
 *
 * @version 1.0
 */
trait NotificationTrait
{
    /**
     * @param Notification      $notification
     * @param ?TypeNotification $typeNotification
     * @param ?ShouldBroadcast  $broadcast
     *
     * @return bool
     */
    protected function sendNotification(Notification $notification, ?TypeNotification $typeNotification = null, ?ShouldBroadcast $broadcast = null): bool
    {
        // phpcs:ignore
        if ($notification->user_id) {
            // phpcs:ignore
            $user = User::findOrFail($notification->user_id);
            $user->notify($typeNotification);
        } else {
            // phpcs:ignore
            $users = User::where('role_id', '=', $notification->role_id)->get();
            ViaNotification::send($users, $typeNotification);
        }

        if ($broadcast) {
            event($broadcast);
        }

        return true;
    }


    /**
     * @param array    $bodyNotification
     * @param int|null $user
     * @param int|null $role
     *
     * @return Notification
     */
    protected function createNotification(array $bodyNotification, ?int $user = null, ?int $role = null): Notification
    {
        $notification = new Notification();
        $notification->notification = $bodyNotification;
        // phpcs:ignore
        $notification->user_id = $user;
        // phpcs:ignore
        $notification->role_id = $role;
        $notification->check = Notification::$UNCHECK;
        $notification->save();

        return $notification;
    }

}
