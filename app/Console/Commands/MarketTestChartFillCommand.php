<?php

namespace App\Console\Commands;

use App\Models\Market\Market;
use App\Models\Order\Order;
use App\Models\Transaction\Transaction;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MarketTestChartFillCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:chart {market} {user=1} {days=1}';

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
        //Transaction::where('id', '!=', 0)->delete();

        // Playground
        $market = Market::with(['baseCurrency', 'quoteCurrency'])->whereName($this->argument('market'))->first();

        if(!$market) {
            $this->info('Wrong Market Name');
            return;
        }

        $user = User::find($this->argument('user'));

        if(!$user) {
            $this->info('Wrong User ID');
            return;
        }

        $price = (float)market_get_stats($market->id, 'last');




        $days = (int)$this->argument('days');



        $carbonDate = Carbon::now();
        $transactions = [];

        $orderType = ['buy', 'sell'];

        for($i=$days; $i>=0; $i--) {

            $price = $price - $market->quote_ticker_size;

            $ratio = rand(10, 20) * 0.1;

            $ratio2 = rand(10, 400) * 0.1;

            $ratio3 = rand(10, 80) * 0.1;

            $ratio4 = rand(10, 100) * 0.1;

            $price2 = ($price - $market->quote_ticker_size) * $ratio;
            $price3 = ($price - $market->quote_ticker_size) * $ratio2;
            $price4 = ($price - $market->quote_ticker_size) * $ratio3;
            $price5 = ($price - $market->quote_ticker_size) * $ratio4;

            $rand = rand(10,59);
            $rand2 = rand(10,59);
            $rand3 = rand(10,59);
            $rand4 = rand(10,59);

            $transactions[] = [
                'market_id' => $market->id,
                'process_id' => Str::uuid(),
                'order_id' => Order::where('market_id', $market->id)->first()->id,
                'user_id' => $user->id,
                'is_maker' => true,
                'order_type' => 'limit',
                'order_side' => $orderType[rand(0,1)],
                'price' => $price2,
                'base_currency' => rand(1,100),
                'quote_currency' => rand(1, 100),
                'created_at' => $carbonDate->format("Y-m-d H:$rand:s"),
                'updated_at' => $carbonDate->format("Y-m-d H:$rand:s"),
            ];


            $transactions[] = [
                'market_id' => $market->id,
                'process_id' => Str::uuid(),
                'order_id' => Order::where('market_id', $market->id)->first()->id,
                'user_id' => $user->id,
                'is_maker' => true,
                'order_type' => 'limit',
                'order_side' => $orderType[rand(0,1)],
                'price' => $price5,
                'base_currency' => rand(50,200),
                'quote_currency' => rand(100, 200),
                'created_at' => $carbonDate->format("Y-m-d H:$rand4:s"),
                'updated_at' => $carbonDate->format("Y-m-d H:$rand4:s"),
            ];

            $this->info($carbonDate->format('d.m.Y H:i:s'));

            $carbonDate->subDay();
        }

        Transaction::insert($transactions);
        dd($transactions);
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
