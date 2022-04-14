<?php

namespace App\Console\Commands;

use App\Models\Market\Market;
use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MarketTestFillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:fill {market} {user=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Playground
        $market = Market::whereName($this->argument('market'))->first();

        if(!$market) {
            $this->info('Wrong Market Name');
            return;
        }

        $user = User::find($this->argument('user'));

        if(!$user) {
            $this->info('Wrong User ID');
            return;
        }

        $price = 0.0559;
        $tickSize = 0.0001;

        $buyPrice = $price;
        $sellPrice = $price;

        $min = 0.010;
        $max = 0.100;

        // Buy Orders
        for($i=0; $i<15; $i++) {
            $buyPrice = $buyPrice - $tickSize;
            $amount = (mt_rand ($min*1000, $max*1000) / 1000);

            $response = Http::withToken('wzrgPrswb4Mj9Z5bSahsGVgnw9y2ef9buZgOPnB3')->post(route('orders.store'), [
                'market' => $market->name,
                'price' => $buyPrice,
                'quantity' => $amount,
                'type' => 'limit',
                'side' => 'buy',
            ]);

            $this->info('Buy Price: ' . $buyPrice);
            $this->info('Buy Amount: ' . $amount);
            $this->info('--------------------------');

            sleep(2);
        }

        $this->info('--------------------------');
        $this->info('--------------------------');
        $this->info('--------------------------');
        $this->info('--------------------------');

        // Sell Orders
        for($i=0; $i<15; $i++) {
            $sellPrice = $sellPrice + $tickSize;
            $amount = (mt_rand ($min*1000, $max*1000) / 1000);

            $response = Http::withToken('wzrgPrswb4Mj9Z5bSahsGVgnw9y2ef9buZgOPnB3')->post(route('orders.store'), [
                'market' => $market->name,
                'price' => $sellPrice,
                'quantity' => $amount,
                'type' => 'limit',
                'side' => 'sell',
            ]);

            sleep(2);

            $this->info('Sell Price: ' . $sellPrice);
            $this->info('Sell Amount: ' . $amount);
            $this->info('--------------------------');
        }

    }
}
