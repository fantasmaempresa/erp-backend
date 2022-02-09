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
class QuoteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('quotes');
    }

    /**
     * Create a new event instance.
     * @param Notification $notification
     * @param int          $user_id
     * @param int          $role_id
     * @param int          $quote_id
     *
     * @return void
     */
    // phpcs:ignore
    public function __construct(public Notification $notification, public int $quote_id, public int $user_id, public int $role_id)
    {
        //
    }

    /**
     * @return array
     */
    #[ArrayShape([
        'notification' => "string",
        'role_id' => "int",
        'user_id' => "int",
        'quote_id' => "int",
    ])]
    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification,
            // phpcs:ignore
            'role_id' => $this->role_id,
            // phpcs:ignore
            'user_id' => $this->user_id,
            // phpcs:ignore
            'quote_id' => $this->quote_id,
        ];
    }
}
