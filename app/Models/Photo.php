<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = ['path', 'caption', 'photoable_id', 'photoable_type'];

    protected $appends = ['url'];

    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

        return Storage::disk($disk)->url($this->path);
    }
}
