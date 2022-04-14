<?php

namespace App\Models\FileUpload;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path'
    ];

    protected $appends = ['url', 'type'];

    public function getUrlAttribute()
    {
        return url($this->path);
    }

    public function getTypeAttribute()
    {
        return 'image';
    }
}
