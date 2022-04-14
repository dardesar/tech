<?php

namespace App\Models\Currency;

use App\Casts\CryptoCurrencyDecimalCast;
use App\Casts\PercentageDecimalCast;
use App\Models\Currency\Traits\Relations\CurrencyRelation;
use App\Models\Currency\Traits\Scopes\CurrencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    public $table = 'currencies';

    use HasFactory, SoftDeletes, CurrencyRelation, CurrencyScope;

    public $fillable = [
        'name',
        'symbol',
        'alt_symbol',
        'type',
        'decimals',
        'status',
        'bank_account',
        'deposit_status',
        'withdraw_status',
        'file_id',
        'deposit_fee',
        'withdraw_fee',
        'min_deposit',
        'max_deposit',
        'min_withdraw',
        'max_withdraw',
        'min_deposit_confirmation',
        'contract',
        'bank_status',
        'cc_status',
        'cc_exchange_rate',
        'has_payment_id',
        'txn_explorer'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deposit_status' => 'boolean',
        'withdraw_status' => 'boolean',
        'bank_status' => 'boolean',
        'cc_status' => 'boolean',
        'deposit_fee'=> PercentageDecimalCast::class,
        'withdraw_fee'=> PercentageDecimalCast::class,
        'min_deposit'=> CryptoCurrencyDecimalCast::class,
        'min_withdraw'=> CryptoCurrencyDecimalCast::class,
        'max_withdraw'=> CryptoCurrencyDecimalCast::class,
        'wallet_balance' => CryptoCurrencyDecimalCast::class,
    ];
}
