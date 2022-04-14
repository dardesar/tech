<?php

namespace App\Models\Language\Traits\Scopes;

trait LanguageScope
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}

