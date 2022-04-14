<?php

namespace App\Events;

use App\Models\Order\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Order\Order as OrderResource;

class OrderBookUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $market;
    public $type;
    public $quantity;

    public $queue = 'events';

    /**
     * Create a new event instance.
     *
     * @param Order $order
     * @param String $type
     */
    public function __construct(Order $order, $type, $quantity = 0)
    {
        $this->order = $order;
        $this->market = $order->market;
        $this->type = $type;
        $this->quantity = $quantity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('orderbook-' . $this->market->name);
    }

    public function broadcastWith()
    {
        $custom_fields = [];

        if($this->type == "update") {
            $custom_fields = ['updated_quantity' => $this->quantity];
        }

        $order = new OrderResource($this->order, $custom_fields);

        return [
            'order' => $order,
            'type' => $this->type
        ];
    }
}
