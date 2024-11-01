<?php

namespace App\Events;

use App\Models\Process;
use App\Models\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectActionsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public array $action,
        public Project $project,
        public Process | null $process,
        public array $data = [],
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('project-actions');
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'project_id' => $this->project->id,
            'process_id' => $this->process->id ?? null,
            'data' => $this->data,
        ];
    }
}
