<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Item extends Model
{
    protected $fillable = ['name', 'aliases', 'description', 'tags', 'location_id'];

    protected $casts = [
        'aliases' => 'array',
        'tags' => 'array',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    /** Returns a compact string for the AI index snapshot. */
    public function toSearchEntry(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'aliases' => $this->aliases ?? [],
            'description' => $this->description,
            'tags' => $this->tags ?? [],
            'location' => $this->location?->fullPath(),
            'location_id' => $this->location_id,
        ];
    }
}
