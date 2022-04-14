<?php

namespace Database\Seeders\Currency;

use App\Models\Network\Network;
use Illuminate\Database\Seeder;

class CoinTypeSeeder extends Seeder
{
    const ALLOWED_COIN_TYPES = [
        'coinpayments' => [
            'id' => 1,
            'type' => 'coin',
            'name' => 'Coinpayments API'
        ],
        'eth' => [
            'id' => 2,
            'type' => 'coin',
            'name' => 'ETH API'
        ],
        'erc20' => [
            'id' => 3,
            'type' => 'coin',
            'name' => 'ERC-20 API'
        ],
        'bank_credit_card' => [
            'id' => 4,
            'type' => 'fiat',
            'name' => 'Bank Transfer & Credit Card'
        ],
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::ALLOWED_COIN_TYPES as $slug=>$data) {

            if(Network::whereSlug($slug)->first()) continue;

            $network = new Network();
            $network->id = $data['id'];
            $network->name = $data['name'];
            $network->type = $data['type'];
            $network->slug = $slug;
            $network->save();
        }
    }
}
