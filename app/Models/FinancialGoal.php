<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FinancialGoal extends Model
{
    protected $fillable = [
        'user_id', 'title', 'category', 'target_amount',
        'current_amount', 'target_date', 'priority', 'status',
    ];

    protected $casts = [
        'target_amount'  => 'float',
        'current_amount' => 'float',
        'target_date'    => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getProgressPercentAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->target_date) return null;
        return max(0, (int) now()->diffInDays($this->target_date, false));
    }

    public function getMonthlyRequiredAttribute(): ?float
    {
        $months = $this->target_date ? max(1, now()->diffInMonths($this->target_date, false)) : null;
        if (!$months || $months <= 0) return null;
        return round($this->remaining_amount / $months, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function isOnTrack(): bool
    {
        if (!$this->target_date || $this->status !== 'active') return false;
        $totalDays = Carbon::parse($this->created_at)->diffInDays($this->target_date);
        $elapsedDays = Carbon::parse($this->created_at)->diffInDays(now());
        if ($totalDays <= 0) return $this->progress_percent >= 100;
        $expectedProgress = ($elapsedDays / $totalDays) * 100;
        return $this->progress_percent >= ($expectedProgress * 0.85); // 15% buffer
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}
