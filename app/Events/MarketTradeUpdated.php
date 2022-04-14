<?php

namespace App\Events;

use App\Models\Order\Order;
use App\Models\Transaction\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Transaction\Transaction as TransactionResource;

class MarketTradeUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $market;
    public $transaction;

    public $queue = 'events';

    /**
     * Create a new event instance.
     *
     * @param Order $order
     * @param String $type
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->market = $transaction->market;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('market');
    }

    public function broadcastWith()
    {
        $transaction = new TransactionResource($this->transaction);

        return [
            'market' => $this->market,
            'trade' => $transaction,
        ];
    }
}
