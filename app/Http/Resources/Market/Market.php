<?php

namespace App\Http\Resources\Market;

use Illuminate\Http\Resources\Json\JsonResource;

class Market extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'base_currency' => $this->baseCurrency->symbol,
            'base_currency_name' => $this->baseCurrency->name,
            'base_currency_logo' => url($this->baseCurrency->file->path),
            'quote_currency' => $this->quoteCurrency->symbol,
            'quote_currency_name' => $this->quoteCurrency->name,
            'base_precision' => $this->base_precision,
            'quote_precision' => $this->quote_precision,
            'min_trade_size' => $this->min_trade_size,
            'max_trade_size' => $this->max_trade_size,
            'min_trade_value' => $this->min_trade_value,
            'max_trade_value' => $this->max_trade_value,
            'base_ticker_size' => $this->base_ticker_size,
            'quote_ticker_size' => $this->quote_ticker_size,
            'status' => $this->status,
            'trade_status' => $this->trade_status,
            'buy_order_status' => $this->buy_order_status,
            'sell_order_status' => $this->sell_order_status,
            'cancel_order_status' => $this->cancel_order_status,
            'last' => math_formatter(market_get_stats($this->id, 'last'), $this->base_precision),
            'change' => math_percentage_between(market_get_stats($this->id, 'last'), $this->last),
            'high' => math_formatter(market_get_stats($this->id, 'high'), $this->base_precision),
            'low' => math_formatter(market_get_stats($this->id, 'low'), $this->base_precision),
            'volume' => math_formatter(market_get_stats($this->id, 'volume'), $this->quote_precision),
            'capitalization' => math_formatter(market_get_stats($this->id, 'capitalization'), $this->base_precision),
        ];
    }
}
