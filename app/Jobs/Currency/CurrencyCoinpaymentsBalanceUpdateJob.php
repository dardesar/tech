<?php

namespace App\Jobs\Currency;

use App\Models\Currency\Currency;
use App\Services\PaymentGateways\Coin\Coinpayments\Api\CoinpaymentsGateway;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CurrencyCoinpaymentsBalanceUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $currency;

    public function onQueue($queue)
    {
        $this->queue = 'default';
        return $this;
    }

    /**
     * Create a new job instance.
     *
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $balance = null;

            switch ($this->currency->networks->first()->id) {
                case NETWORK_COINPAYMENTS:
                    break;
            }

            $balances = (new CoinpaymentsGateway())->getBalance();

            if ($balance !== null) {
                $this->currency->wallet_balance = $balance;
                $this->currency->wallet_balance_updated_at = Carbon::now();
                $this->currency->update();
            }

        } catch (\Exception $e) {

            Log::info('Wallet balance was not updated');
            Log::error($e);

        }

    }
}
