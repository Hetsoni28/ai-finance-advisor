<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'payment_id',
        'amount_paid',
    ];

    protected $casts = [
        'start_date'  => 'datetime',
        'end_date'    => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        // Free plan never expires
        if ($this->end_date === null) {
            return false;
        }

        return Carbon::parse($this->end_date)->isPast();
    }

    /**
     * Get remaining days.
     */
    public function getRemainingDaysAttribute(): int
    {
        if ($this->end_date === null) {
            return 999; // Lifetime
        }

        $remaining = (int) now()->diffInDays(Carbon::parse($this->end_date), false);
        return max(0, $remaining);
    }

    /**
     * Get human-readable remaining text.
     */
    public function getRemainingTextAttribute(): string
    {
        if ($this->end_date === null) {
            return 'Lifetime Access';
        }

        $days = $this->remaining_days;

        if ($days === 0) {
            return 'Expires today';
        }

        if ($days === 1) {
            return '1 day remaining';
        }

        return $days . ' days remaining';
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO-EXPIRY CHECK
    |--------------------------------------------------------------------------
    */

    /**
     * Check and mark expired subscriptions.
     */
    public function checkAndExpire(): bool
    {
        if ($this->isExpired() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
