<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * =========================================
     * RELATIONSHIPS
     * =========================================
     */

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
        // If using pivot table instead:
        // return $this->belongsToMany(User::class);
    }

    /**
     * =========================================
     * SCOPES
     * =========================================
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * =========================================
     * HELPERS
     * =========================================
     */

    public function matchesSlug(string $slug): bool
    {
        return $this->slug === $slug;
    }
}