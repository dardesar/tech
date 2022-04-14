<?php

namespace App\Repositories\Currency;

use App\Interfaces\Currency\CurrencyRepositoryInterface;
use App\Models\Currency\Currency;
use App\Models\Wallet\Wallet;
use App\Services\PaymentGateways\Coin\Coinpayments\Model\CoinpaymentsCurrency;
use App\Services\PaymentGateways\Coin\Ethereum\Api\EthereumGateway;
use Auth;
use Illuminate\Support\Facades\DB;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * @var Currency
     */
    protected $currency;

    /**
     * CurrencyRepository constructor.
     *
     */
    public function __construct()
    {
        $this->currency = new Currency();
    }

    public function get($id, $trashed = false, $dashboard = false, $relations = null) {

        $currency = Currency::whereId($id);

        if($relations === null) {
            $currency->with(['networks', 'file']);
        } else {
            $currency->with($relations);
        }

        if(!$dashboard) {
            $currency->active();
        }

        if($trashed) {
            $currency->withTrashed();
        }

        return $currency->first();
    }

    public function getCurrencyBySymbol($symbol, $type = false, $active = true, $relations = null) {

        $currency = Currency::query();

        $currency->where(function($query) use ($symbol){
            $query->where('alt_symbol', $symbol);
            $query->orWhere('symbol', $symbol);
        });

        if(!$relations) {
            $relations = ['networks'];
        }

        $currency->with($relations);

        if($type) {
            $currency->whereType($type);
        }

        if($active) {
            $currency->active();
        }

        return $currency->first();
    }

    public function getCurrencyByContract($contract, $type = false, $active = true) {

        $currency = Currency::query();

        $currency->where('contract', $contract);

        $currency->with('networks');

        if($type) {
            $currency->whereType($type);
        }

        if($active) {
            $currency->active();
        }

        return $currency->first();
    }

    public function all($paginate, $dashboard = false, $relations = ['file'], $type = false) {

        $currencies = Currency::filter(request()->only(['search', 'trashed', 'type']))->orderByLatest();

        if(!$dashboard) {
            $currencies->active();
        }

        if($type) {
            $currencies->type($type);
        }

        $currencies->with($relations);

        if($paginate) {
            return $currencies->paginate(30)->withQueryString();
        } else {
            return $currencies->get();
        }
    }

    public function count() {
        $currency = Currency::query();
        return $currency->count();
    }

    public function store($data) {

        if(in_array(NETWORK_COINPAYMENTS, $data['networks'])) {
            $coinpaymentCurrency = CoinpaymentsCurrency::where('symbol', $data['alt_symbol'])->first();
            $data['txn_explorer'] = $coinpaymentCurrency->blockchain_url;
            $data['has_payment_id'] = $coinpaymentCurrency && $coinpaymentCurrency->has_payment_id;
        }

        $currency = $this->currency->create($data);
        $currency->networks()->sync($data['networks']);

        // Store Ethereum Contract Address
        if(in_array(NETWORK_ERC, $data['networks'])) {
            (new EthereumGateway())->registerContract($data['contract']);
        }

        if(in_array(NETWORK_ERC, $data['networks']) || in_array(NETWORK_ETH, $data['networks'])) {
            $data['txn_explorer'] = 'https://etherscan.io/tx/%txid%';
        }

        return $currency->fresh();
    }

    public function update($id, $data) {

        $currency = Currency::withTrashed()->find($id);
        $currency->update($data);
        $currency->networks()->sync($data['networks']);

        if(in_array(NETWORK_ERC, $data['networks'])) {
            (new EthereumGateway())->registerContract($currency->contract);
        }

        return $currency->fresh();
    }

    public function delete($id) {

        $currency = Currency::find($id);
        $currency->delete();

        return true;
    }

    public function restore($id) {

        $currency = Currency::withTrashed()->find($id);
        $currency->restore();

        return true;
    }

    public function getQuoteCurrencies() {
        return DB::table('currencies')->select('symbol')->whereRaw('id IN (SELECT quote_currency_id FROM markets GROUP BY quote_currency_id)')->orderBy('symbol','asc')->pluck('symbol');
    }

    public function getReport($filters = [], $pagination = true) {

        $currency = Currency::query();

        $currency->filter($filters)->orderBy('name', 'asc');

        if(!$pagination) {
            return $currency->get();
        }

        return $currency->paginate(150)->withQueryString();
    }
}
