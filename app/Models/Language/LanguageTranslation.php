<?php

namespace App\Models\Language;

use App\Models\Language\Traits\Scopes\LanguageTranslationScope;
use Illuminate\Database\Eloquent\Model;

class LanguageTranslation extends Model
{
    use LanguageTranslationScope;

    public $fillable = [
        'key',
        'content',
        'language_id',
    ];
}
