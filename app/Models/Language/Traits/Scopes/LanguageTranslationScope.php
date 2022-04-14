<?php

namespace App\Models\Language\Traits\Scopes;

trait LanguageTranslationScope
{
    public function scopeOrderByAsc($query)
    {
        return $query->orderBy('key', 'asc');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('key', 'like', '%'.$search.'%');
                $query->orWhere('content', 'like', '%'.$search.'%');
            });
        });
    }
}

