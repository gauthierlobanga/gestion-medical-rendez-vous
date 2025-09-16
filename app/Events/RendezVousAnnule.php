<?php

namespace App\Events;

use App\Models\Rendezvous;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RendezVousAnnule
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rendezvous;
    /**
     * Create a new event instance.
     */

    public function __construct(Rendezvous $rendezvous)
    {
        $this->rendezvous = $rendezvous;
    }
}
