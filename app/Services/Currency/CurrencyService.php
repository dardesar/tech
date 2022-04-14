<?php

namespace App\Services\Currency;

use App\Models\Currency\Currency;
use App\Repositories\Currency\CurrencyRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CurrencyService {

    private $currencyRepository;

    public function __construct()
    {
        $this->currencyRepository = new CurrencyRepository();
    }

    public function getCurrencies($paginate = true, $dashboard = false) {
        return $this->currencyRepository->all($paginate, $dashboard);
    }

    public function getCurrency($id, $trashed, $dashboard = false) {
        return $this->currencyRepository->get($id, $trashed, $dashboard);
    }

    public function getCurrencyBySymbol($symbol, $type = false, $active = true, $relations = null) {
        return $this->currencyRepository->getCurrencyBySymbol($symbol, $type, $active, $relations);
    }

    public function storeCurrency() {
        return $this->currencyRepository->store(request()->all());
    }

    public function updateCurrency($id) {
        return $this->currencyRepository->update(
            $id,
            request()->all()
        );
    }

    public function deleteCurrency($id) {
        return $this->currencyRepository->delete($id);
    }

    public function restoreCurrency($id) {
        return $this->currencyRepository->restore($id);
    }

    public function calculateSystemFee($type, $model, $amount) {

        $field = $type == 'deposit' ? 'deposit_fee' : 'withdraw_fee';

        $rate = $model->{$field};

        if($rate == 0) return 0;

        return math_percentage($amount, $rate);
    }

    public function increase(Currency $currency, $quantity) {
        DB::table('currencies')
            ->where('id', $currency->id)
            ->update([
                'wallet_balance' => DB::raw("wallet_balance + $quantity"),
                'wallet_balance_updated_at' => Carbon::now(),
            ]);
    }

    public function decrease(Currency $currency, $quantity) {
        DB::table('currencies')
            ->where('id', $currency->id)
            ->update([
                'wallet_balance' => DB::raw("wallet_balance - $quantity"),
                'wallet_balance_updated_at' => Carbon::now(),
            ]);
    }
}
