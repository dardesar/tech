<?php

namespace App\Models\Deposit;

use App\Casts\CryptoCurrencyDecimalCast;
use App\Models\Deposit\Traits\Relations\DepositRelation;
use App\Models\Deposit\Traits\Scopes\DepositScope;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use DepositRelation, DepositScope;

    protected $hidden = [
        'raw',
        'initial_raw'
    ];

    public $fillable = [
        'deposit_id',
        'txn',
        'source_id',
        'currency_id',
        'type',
        'network_id',
        'amount',
        'network_fee',
        'system_fee',
        'address',
        'user_id',
        'confirms',
        'status',
        'initial_raw',
        'raw'
    ];

    public $appends = [
        'txn_link'
    ];

    protected $casts = [
        'amount' => CryptoCurrencyDecimalCast::class,
        'network_fee' => CryptoCurrencyDecimalCast::class,
        'system_fee' => CryptoCurrencyDecimalCast::class,
        'created_at' => "datetime:Y-m-d H:i:s",
    ];

    public function getTxnLinkAttribute()
    {
        if (!$this->txn) return null;

        return str_replace('%txid%', $this->txn, $this->currency->txn_explorer);
    }
}
