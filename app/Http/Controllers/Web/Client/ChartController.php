<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Market\MarketRepository;
use App\Services\Market\MarketService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    protected $candles = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($symbol)
    {
        $marketRepository = new MarketRepository();

        $market = $marketRepository->get($symbol);

        return view('chart.index', [
            'symbol' => $market->name,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function symbols(Request $request)
    {
        $marketRepository = new MarketRepository();
        $market = $marketRepository->get($request->get('symbol'));

        if(!$market) return response()->json([]);

        return response()->json([
            'ticker' => $market->name,
            'name' => $market->name,
            'symbol' => $market->name,
            'intraday_multipliers' => MARKET_CHART_RESOLUTION,
            'exchange' => config('app.name'),
            'has_no_volume' => false,
            'minmov' => 1,
            'minmov2' => 0,
            'has_intraday' => true,
            'has_empty_bars' => true,
            'type' => MARKET_CHART_TYPE,
            'pricescale' => math_decimal_scale_count($market->quote_ticker_size),
            'session' => '24x7',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $marketRepository = new MarketRepository();
        $market = $marketRepository->get($request->get('symbol'));

        $marketService = new MarketService();
        $transactions = $marketService->getCandles($market);

        if($transactions && $transactions->count() > 0) {
            $transactions->each(function ($transaction) {
                $this->candles['v'][] = $transaction->volume;
                $this->candles['t2'][] = str_replace('.000000', '', $transaction->date);
                $this->candles['t'][] = Carbon::parse(str_replace('.000000', '', $transaction->date))->timestamp;
                $this->candles['o'][] = $transaction->open;
                $this->candles['c'][] = $transaction->close;
                $this->candles['h'][] = $transaction->high;
                $this->candles['l'][] = $transaction->low;
            });
            $this->candles['s'] = 'ok';
        } else {
            $this->candles['s'] = 'no_data';
        }

        return response()->json($this->candles);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function config()
    {
        return response()->json(MARKET_CHART_CONFIGS);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function time()
    {
        return response()->json(Carbon::now()->timestamp);
    }

    /**
     * @param Request $request
     */
    public function candles(Request $request) {

    }
}
