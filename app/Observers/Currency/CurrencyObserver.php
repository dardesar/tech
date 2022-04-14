<?php

namespace App\Observers\Currency;

use App\Jobs\Wallet\CreateWalletsForCurrencyJob;
use App\Models\Currency\Currency;

class CurrencyObserver
{
    /**
     * Listen to the Currency created event.
     *
     * @param  \App\Models\Currency\Currency $currency
     * @return void
     */
    public function created(Currency $currency)
    {
        $job = new CreateWalletsForCurrencyJob($currency);

        dispatch_now($job);
    }
}
