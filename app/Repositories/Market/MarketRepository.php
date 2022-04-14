<?php

namespace App\Repositories\Market;

use App\Events\MarketStatsUpdated;
use App\Interfaces\Market\MarketRepositoryInterface;
use App\Models\Market\Market;
use App\Models\Market\MarketAdmin;
use App\Models\Transaction\Transaction;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketRepository implements MarketRepositoryInterface
{
    /**
     * @var Market
     */
    protected $market;

    /**
     * MarketRepository constructor.
     *
     */
    public function __construct()
    {
        $this->market = new Market();
    }

    /**
     * @param $market
     * @param false $trashed
     * @param false $dashboard
     * @return mixed
     */
    public function get($market, $trashed = false, $dashboard = false) {

        if(is_numeric($market)) {
            $model = Market::whereId($market);
        } else {
            $model = Market::whereName($market);
        }

        if($trashed) {
            $model->withTrashed();
        }

        if(!$dashboard) {
            $model->active();
        }

        return $model->first();
    }

    /**
     * @param $paginate
     * @param false $dashboard
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function all($paginate, $dashboard = false) {

        $market = Market::query();

        if(!$dashboard) {
            $market->active();
        }

        if($paginate) {
            return $market->with(['baseCurrency', 'quoteCurrency'])
                ->filter(request()->only(['search', 'trashed']))
                ->orderByLatest()
                ->paginate(30)
                ->withQueryString();
        }

        return $market->get();
    }

    public function count() {
        $market = Market::query();
        return $market->count();
    }

    /**
     * @param $data
     * @return Market
     */
    public function store($data) {

        $market = $this->market->create($data);

        market_set_stats($market->id, 'last', $market->last);

        return $this->market->fresh();
    }

    /**
     * @param $id
     * @param $data
     * @return Market
     */
    public function update($id, $data) {

        $market = MarketAdmin::withTrashed()->find($id);

        $market->update($data);

        market_set_stats($id, 'last', $data['last']);

        $market = $market->fresh();

        if($market->status) {
            event(new MarketStatsUpdated($market));
        }

        return $market;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id) {

        $market = Market::find($id);
        $market->delete();

        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function restore($id) {

        $market = Market::withTrashed()->find($id);
        $market->restore();

        return true;
    }

    /**
     * @param $market
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getTrades($market) {

        $model = Market::whereName($market)->first();

        return Transaction::where('market_id', $model->id)->taker()->orderByLatest()->limit(20)->get();
    }

    /**
     * @param $market
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCandles($market) {

        $from = intval(request()->get('from'));
        $to = intval(request()->get('to'));

        $rangeInSeconds = $from - $to;

        if($rangeInSeconds > 3000000) {
            $to = $from;
        }

        $resolution = request()->get('resolution');

        $interval = MARKET_RESOLUTION_ASSOC[$resolution] ?? false;

        if(!$interval) return false;

        $transaction = Transaction::query();

        $transaction->selectRaw("DATE_FORMAT(MIN(created_at), '%d-%m-%Y %H:%i:00') as date2")
            ->selectRaw('FROM_UNIXTIME(FLOOR((UNIX_TIMESTAMP(created_at))/ ? ) * ?) AS date', [$interval, $interval])
            ->selectRaw('SUM(quote_currency) as volume')
            ->selectRaw('MAX(price) as high')
            ->selectRaw('MIN(price) as low')
            ->selectRaw("SUBSTRING_INDEX(MAX(CONCAT(created_at, '_', price)), '_', -1) as close")
            ->selectRaw("SUBSTRING_INDEX(MIN(CONCAT(created_at, '_', price)), '_', -1) as open");

        if($market) {
            $transaction->where('market_id', $market->id);
        }

        $transaction->whereRaw('UNIX_TIMESTAMP(created_at) > ?', $from)
            ->whereRaw('UNIX_TIMESTAMP(created_at) < ?', $to)
            ->maker()
            ->groupByRaw('date')
            ->orderByRaw('date ASC');

        return $transaction->get();
    }

    /**
     * @param $market
     * @return int
     */
    public function getCapitalization($market) {
        return DB::table('orders')->where('market_id', $market)->sum('quantity');
    }
}
