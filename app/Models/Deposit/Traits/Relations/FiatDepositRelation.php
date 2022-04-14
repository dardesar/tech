<?php

namespace App\Models\Deposit\Traits\Relations;

use App\Models\Currency\Currency;
use App\Models\FileUpload\FileUpload;
use App\Models\User\User;

trait FiatDepositRelation
{
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function receipt()
    {
        return $this->belongsTo(FileUpload::class, 'receipt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


