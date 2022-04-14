<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Market\Market as MarketResource;
use App\Models\Market\Market;
use App\Repositories\Currency\CurrencyRepository;
use App\Services\Market\MarketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Market/Markets');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Market $market)
    {
        $market = (new MarketService())->getMarket($market->id);

        if(!$market) {
            throw new ModelNotFoundException();
        }

        $currencyRepository = (new CurrencyRepository())->getQuoteCurrencies();

        return Inertia::render('Market/Market', [
            'market' => new MarketResource($market),
            'quotes' => $currencyRepository
        ]);
    }
}
