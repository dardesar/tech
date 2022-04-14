<?php

namespace App\Models\Network;

use App\Models\Network\Traits\Scopes\NetworkScope;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use NetworkScope;

    public $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
