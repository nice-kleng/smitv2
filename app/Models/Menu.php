<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    // Tambahkan konstanta untuk module
    public const MODULES = [
        'admin' => 'Admin',
        'helpdesk' => 'Helpdesk',
        'inventory' => 'Inventory',
    ];

    protected $fillable = [
        'name',
        'icon',
        'route',
        'module',
        'permission_name',
        'parent_id',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Tambahkan return type declarations
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->orderBy('order');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()
            ->where('is_active', true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_name', 'name');
    }

    // Tambahkan scope untuk active menus
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeAccessibleBy($query, $user): Builder
    {
        if ($user->hasRole('superadmin')) {
            return $query;
        }

        $permissionNames = $user->getAllPermissions()->pluck('name');

        return $query->where(function ($q) use ($permissionNames) {
            $q->whereNull('permission_name')
                ->orWhereIn('permission_name', $permissionNames);
        });
    }
}
