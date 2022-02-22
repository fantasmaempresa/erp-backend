<?php
/**
 * CODE
 * Quote Event Class
 */

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     * @param Notification $notification
     * @param int          $user_id
     * @param int          $role_id
     * @param array        $extra_info
     *
     * @return void
     */
    // phpcs:ignore
    public function __construct(public Notification $notification, public int $user_id, public int $role_id, public array $extra_info)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('notification');
    }

    /**
     * @return array
     */
    #[ArrayShape([
        'notification' => "string",
        'role_id' => "int",
        'user_id' => "int",
        'extra_info' => "int",
    ])]
    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification,
            // phpcs:ignore
            'extra_info' => $this->extra_info,
            // phpcs:ignore
            'role_id' => $this->role_id,
            // phpcs:ignore
            'user_id' => $this->user_id,
        ];
    }
}
