<?php

namespace App\Models\Withdrawal;

use App\Casts\CryptoCurrencyDecimalCast;
use App\Models\Withdrawal\Traits\Relations\WithdrawalRelation;
use App\Models\Withdrawal\Traits\Scopes\WithdrawalScope;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use WithdrawalRelation, WithdrawalScope;

    protected $hidden = [
        'raw',
        'initial_raw'
    ];

    public $fillable = [
        'withdrawal_id',
        'txn',
        'source_id',
        'currency_id',
        'type',
        'network_id',
        'amount',
        'fee',
        'address',
        'payment_id',
        'user_id',
        'confirms',
        'status',
        'rejected_reason',
        'initial_raw',
        'raw'
    ];

    public $appends = [
        'txn_link'
    ];

    protected $casts = [
        'amount' => CryptoCurrencyDecimalCast::class,
        'fee' => CryptoCurrencyDecimalCast::class,
        'created_at' => "datetime:Y-m-d H:i:s",
    ];

    public function getTxnLinkAttribute()
    {
        if (!$this->txn) return null;

        return str_replace('%txid%', $this->txn, $this->currency->txn_explorer);
    }
}
