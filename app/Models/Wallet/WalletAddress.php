<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletAddress extends Model
{
    use SoftDeletes;

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
