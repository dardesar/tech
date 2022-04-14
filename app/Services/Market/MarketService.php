<?php

namespace App\Services\Market;

use App\Events\MarketStatsUpdated;
use App\Models\Market\Market;
use App\Repositories\Market\MarketRepository;

class MarketService {

    private $marketRepository;

    public function __construct()
    {
        $this->marketRepository = new MarketRepository();
    }

    public function getMarkets($paginate = true, $dashboard = false) {
        return $this->marketRepository->all($paginate, $dashboard);
    }

    public function getMarket($id, $trashed = false, $dashboard = false) {
        return $this->marketRepository->get($id, $trashed, $dashboard);
    }

    public function storeMarket() {
        return $this->marketRepository->store(request()->all());
    }

    public function updateMarket($id) {
        return $this->marketRepository->update($id, request()->all());
    }

    public function deleteMarket($id) {
        return $this->marketRepository->delete($id);
    }

    public function restoreMarket($id) {
        return $this->marketRepository->restore($id);
    }

    public function getTrades($market) {
        return $this->marketRepository->getTrades($market);
    }

    public function getCandles($market = null) {
        return $this->marketRepository->getCandles($market);
    }

    public function updateStats($market_id, $price, $volume) {
        market_set_stats($market_id, 'last', $price);
        market_set_stats($market_id, 'high', $price);
        market_set_stats($market_id, 'low', $price);
        market_set_stats($market_id, 'volume', $volume);
    }

    public function calculateMarketCapitalization(Market $market) {
        $capitalization = $this->marketRepository->getCapitalization($market->id);
        market_set_stats($market->id, 'capitalization', $capitalization);

        event(new MarketStatsUpdated($market));
    }
}
