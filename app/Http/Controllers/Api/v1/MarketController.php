<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Market\MarketDataRequest;
use App\Http\Resources\Market\Market;
use App\Http\Resources\Market\MarketCollection;
use App\Http\Resources\Order\OrderCollection;
use App\Http\Resources\Transaction\Candles\CandleCollection;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Models\Order\Order;
use App\Repositories\Order\OrderRepository;
use App\Services\Market\MarketService;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    /**
     * @var marketService
     */
    protected $marketService;

    /**
     * PostController Constructor
     *
     * @param MarketService $marketService
     *
     */
    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ticker(Request $request)
    {
        $market = $request->get('market', null);

        if($market) {
            return new Market($this->marketService->getMarket($market));
        }

        return new MarketCollection($this->marketService->getMarkets(false));
    }

    public function orderbook(MarketDataRequest $request) {

        $market = $request->get('market');

        $orderRepository = new OrderRepository();

        return [
            'bids' => new OrderCollection($orderRepository->get($market, Order::SIDE_BUY)),
            'asks' => new OrderCollection($orderRepository->get($market, Order::SIDE_SELL)),
        ];

    }

    /**
     * Display a listing of market trades
     *
     * @return \Illuminate\Http\Response
     */
    public function trades(MarketDataRequest $request)
    {
        $market = $request->get('market');

        return new TransactionCollection($this->marketService->getTrades($market));
    }

    /**
     * Display a listing of market trades
     *
     * @return \Illuminate\Http\Response
     */
    public function candles(MarketDataRequest $request)
    {
        return new CandleCollection($this->marketService->getCandles());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(null, STATUS_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response()->json(null, STATUS_NOT_FOUND);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(null, STATUS_NOT_FOUND);
    }
}
