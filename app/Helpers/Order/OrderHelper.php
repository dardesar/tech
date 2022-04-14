<?php

// Plain Order Helper Functions

use App\Models\Order\Order;

const INITIAL_TRADE_MAKER_FEE = 0.1;
const INITIAL_TRADE_TAKER_FEE = 0.25;

const INITIAL_REFERRAL_FEE = 10;

const ORDER_STATUS_ACTIVE = 'active';
const ORDER_STATUS_FILLED = 'filled';
const ORDER_STATUS_PARTIALLY_FILLED = 'partially_filled';
const ORDER_STATUS_CANCELLED = 'cancelled';

/*
 * Check if order is limit
 */
if (!function_exists('order_is_limit')) {
    function order_is_limit($type)
    {
        return $type === Order::TYPE_LIMIT;
    }
}

/*
 * Check if buy order
 */
if (!function_exists('order_is_buy')) {
    function order_is_buy($type)
    {
        return $type === Order::SIDE_BUY;
    }
}

/*
 * Check if order is market
 */
if (!function_exists('order_is_market')) {
    function order_is_market($type)
    {
        return $type === Order::TYPE_MARKET;
    }
}

/*
 * Check if order is buy market
 */
if (!function_exists('order_is_buy_market')) {
    function order_is_buy_market($type, $side)
    {
        return $type === Order::TYPE_MARKET && $side === Order::SIDE_BUY;
    }
}

/*
 * Check if order is sell market
 */
if (!function_exists('order_is_sell_market')) {
    function order_is_sell_market($type, $side)
    {
        return $type === Order::TYPE_MARKET && $side === Order::SIDE_SELL;
    }
}

/*
 * Check if order type is supported by exchange
 */
if (!function_exists('order_allowed_types')) {
    function order_allowed_types($type)
    {
        return $type === Order::TYPE_MARKET
            || $type === Order::TYPE_LIMIT
            || $type === Order::TYPE_STOP_LIMIT;
    }
}

/*
 * Check if order type is stop limit
 */
if (!function_exists('order_is_stop_limit')) {
    function order_is_stop_limit($type)
    {
        return $type === Order::TYPE_STOP_LIMIT;
    }
}

/*
 * Check if order type is stop limit
 */
if (!function_exists('order_limit_should_be_processed')) {
    function order_limit_should_be_processed($order, $market, $price, $condition)
    {
        $market_price = market_get_stats($market, 'last');

        $shouldBeProcessed = false;

        // Check if price drops condition
        if($condition == Order::STOP_LIMIT_CONDITION_DOWN && $market_price <= $price) {
            $shouldBeProcessed = true;
        }

        if($condition == Order::STOP_LIMIT_CONDITION_UP && $market_price >= $price) {
            $shouldBeProcessed = true;
        }

        if(!$shouldBeProcessed) return false;

        // Load order if only uuid provided
        if(is_string($order)) {
            $order = Order::find($order);
        }

        // Change stop limit type to limit
        $order->update([
           'type' => Order::TYPE_LIMIT
        ]);

        return true;
    }
}

