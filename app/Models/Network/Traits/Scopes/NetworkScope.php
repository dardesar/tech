<?php

namespace App\Models\Network\Traits\Scopes;

trait NetworkScope
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
