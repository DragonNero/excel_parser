<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RowProcessed implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $row;
    public $error;

    public function __construct($row, $error = null)
    {
        $this->row = $row;
        $this->error = $error;
    }

    public function broadcastOn()
    {
        return new Channel('excel-import');
    }
}
