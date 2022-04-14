<?php

// Constants
const MARKET_CHART_RESOLUTION = ['1', '5', '15', '30', '60', '240', '720', '1D', '1W', '1M'];
const MARKET_CHART_TYPE = 'bitcoin';
const MARKET_CHART_CONFIGS = [
    'supports_marks' => false,
    'supports_search' => true,
    'supports_time' => true,
    'supports_timescale_marks' => false,
    'supported_resolutions' => MARKET_CHART_RESOLUTION
];

const MARKET_RESOLUTION_ASSOC = [
    '1' => '60',
    '5' => '300',
    '15' => '900',
    '30' => '1800',
    '60' => '3600',
    '240' => '14400',
    '720' => '43200',
    '1D' => '86400',
    '1W' => '604800',
    '1M' => '2592000',
];

// Plain Math PHP Functions

/*
 * Set stats to cache
 */
if (!function_exists('market_set_stats')) {
    function market_set_stats($market_id, $type, $value)
    {
        $cacheKey = 'market.' . $market_id . '.' . $type;

        $marketCache = cache()->get($cacheKey);

        if(!$marketCache) {
            return cache()->set($cacheKey, $value);
        }

        // Set last market price
        if($type == "last") {
            return cache()->set($cacheKey, $value);
        }

        // Set market 24h high
        if($type == "high" && $value > $marketCache) {
            return cache()->set($cacheKey, $value);
        }

        // Set market 24h low
        if($type == "low" && $value < $marketCache) {
            return cache()->set($cacheKey, $value);
        }

        // Set market 24h volume
        if($type == "volume") {
            return cache()->increment($cacheKey, $value);
        }

        // Set market 24h capitalization
        if($type == "capitalization") {
            return cache()->set($cacheKey, $value);
        }
    }
}

/*
 * Get stats from cache
 */
if (!function_exists('market_get_stats')) {
    function market_get_stats($market_id, $type)
    {
        $cacheKey = 'market.' . $market_id . '.' . $type;

        return cache()->get($cacheKey) ?? 0.00;
    }
}

/*
 * Is Market Tradable
 */
if (!function_exists('market_is_tradable')) {
    function market_is_tradable($market)
    {
        return $market->trade_status;
    }
}

/*
 * Is Market Tradable with buy orders
 */
if (!function_exists('market_is_buy_orders_allowed')) {
    function market_is_buy_orders_allowed($market)
    {
        return $market->buy_order_status;
    }
}

/*
 * Is Market Tradable with sell orders
 */
if (!function_exists('market_is_sell_orders_allowed')) {
    function market_is_sell_orders_allowed($market)
    {
        return $market->sell_order_status;
    }
}

/*
 * Is Market Cancel order allowed
 */
if (!function_exists('market_is_cancel_orders_allowed')) {
    function market_is_cancel_orders_allowed($market)
    {
        if(!$market) return false;

        return $market->cancel_order_status;
    }
}

/*
 * Is Min Trade Size Followed
 */
if (!function_exists('market_is_order_follow_trade_min_size')) {
    function market_is_order_follow_trade_min_size($market, $quantity)
    {
        if($market->min_trade_size == 0) return true;

        if($quantity >= $market->min_trade_size)

        return $market->cancel_order_status;
    }
}
