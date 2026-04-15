<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PortalLink extends Model
{
    protected $fillable = [
        'title',
        'url',
        'description',
        'icon',
        'image',
        'category',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope: only active links
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return null;
    }

    /**
     * Get display icon — returns icon class or default
     */
    public function getDisplayIconAttribute(): string
    {
        return $this->icon ?: 'fas fa-link';
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return $this->category === 'internal' ? 'Internal' : 'External';
    }

    /**
     * Get category badge class
     */
    public function getCategoryBadgeAttribute(): string
    {
        return $this->category === 'internal' ? 'badge-primary' : 'badge-success';
    }
}
