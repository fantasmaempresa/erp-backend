<?php
/**
 * CODE
 * User Login Event Class
 */

namespace App\Events;

use App\Models\User;
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
class SystemActionsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     * @param User  $user
     * @param array $action
     *
     * @return void
     */
    public function __construct(public User $user, public array $action)
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
        return new Channel('system-actions');
    }

    /**
     * @return User[]
     */
    #[ArrayShape(['user' => "\App\Models\User", 'action' => "array"])]
    public function broadcastWith(): array
    {
        return [
            'user' => $this->user,
            'action' => $this->action,
        ];
    }
}
