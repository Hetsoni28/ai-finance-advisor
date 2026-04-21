<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'features',
        'duration_days',
        'badge_color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features'      => 'array',
        'price'         => 'decimal:2',
        'duration_days' => 'integer',
        'is_active'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isFree(): bool
    {
        return $this->price <= 0;
    }

    public function isPro(): bool
    {
        return $this->slug === 'pro';
    }

    public function isPremium(): bool
    {
        return $this->slug === 'premium';
    }

    /**
     * Get formatted price display
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->isFree() ? 'Free' : '₹' . number_format((float)$this->price, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
