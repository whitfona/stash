<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Location extends Model
{
    protected $fillable = ['name', 'notes', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    /** Returns full path from root to this location, e.g. "Guest Bedroom > Under the Bed > Blue Bin" */
    public function fullPath(): string
    {
        $parts = collect([$this->name]);
        $current = $this;

        while ($current->parent_id) {
            $current = $current->parent;
            $parts->prepend($current->name);
        }

        return $parts->implode(' > ');
    }

    /** Recursively loads all descendants. */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }
}
