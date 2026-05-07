<?php

namespace App\Events;

use App\Models\CountdownSequence;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CountdownSequenceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CountdownSequence $sequence,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('countdown.' . $this->sequence->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sequence.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'sequence' => $this->sequence->load(['items.countdown', 'share']),
        ];
    }
}
