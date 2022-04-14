<?php

namespace App\Http\Resources\Currency;

use App\Http\Resources\Network\NetworkCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class Currency extends JsonResource
{
    public static $wrap = null;

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
            'symbol' => $this->symbol,
            'decimals' => $this->decimals,
            'type' => $this->type,
            'status' => $this->status,
            'min_deposit_confirmation' => $this->min_deposit_confirmation,
            'deposit_status' => $this->deposit_status,
            'withdraw_status' => $this->withdraw_status,
            'deposit_fee' => $this->deposit_fee,
            'min_deposit' => math_formatter($this->min_deposit, $this->decimals),
            'max_deposit' => math_formatter($this->max_deposit, $this->decimals),
            'min_withdraw' => math_formatter($this->min_withdraw, $this->decimals),
            'max_withdraw' => math_formatter($this->max_withdraw, $this->decimals),
            'has_payment_id' => $this->has_payment_id,
            'networks' => new NetworkCollection($this->networks),
        ];
    }
}
