<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class Income extends Model
{
    use HasFactory;

    protected $table = 'incomes';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment Security
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'family_id',
        'amount',
        'source',
        'category', // 🚨 FIX: Added missing category field to prevent silent data loss
        'is_personal',
        'income_date',
        'month',
        'year',
    ];

    /*
    |--------------------------------------------------------------------------
    | Data Casting (Strict Type Safety)
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'amount'       => 'float',        // Defends against string math errors
        'is_personal'  => 'boolean',
        'income_date'  => 'date',
        'month'        => 'integer',
        'year'         => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Enterprise Event Hooks (Database Defenders)
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saving(function (Income $income) {

            // 1. Core Ownership Guard
            if (empty($income->user_id)) {
                throw new InvalidArgumentException('Data Integrity Violation: Income must belong to a valid user_id.');
            }

            // 2. Personal vs Family Guard
            if ($income->is_personal) {
                // Force null to prevent mixed-state data corruption
                $income->family_id = null;
            }

            if (!$income->is_personal && empty($income->family_id)) {
                throw new InvalidArgumentException('Data Integrity Violation: Corporate/Family income requires a valid family_id.');
            }

            // 3. Mathematical Sanity Guard
            if ((float) $income->amount <= 0) {
                throw new InvalidArgumentException('Data Integrity Violation: Income amount must be greater than absolute zero.');
            }

            // 4. Automated Chronology Indexing (Fixes Carbon::parse(null) bug)
            if (empty($income->income_date)) {
                throw new InvalidArgumentException('Data Integrity Violation: income_date cannot be null.');
            }

            $date = $income->income_date instanceof Carbon
                ? $income->income_date
                : Carbon::parse($income->income_date);

            $income->month = $date->month;
            $income->year  = $date->year;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relational Architecture
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes (High-Performance Data Retrieval)
    |--------------------------------------------------------------------------
    */

    public function scopePersonal(Builder $query): Builder
    {
        return $query
            ->where('is_personal', true)
            ->whereNull('family_id');
    }

    public function scopeForFamily(Builder $query, int $familyId): Builder
    {
        return $query
            ->where('is_personal', false)
            ->where('family_id', $familyId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth(Builder $query, int $month, int $year): Builder
    {
        return $query
            ->where('month', $month)
            ->where('year', $year);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query
            ->orderByDesc('income_date')
            ->orderByDesc('id');
    }

    /**
     * Identifies high-value deposits for the AI Security Dashboard.
     */
    public function scopeAnomalous(Builder $query, float $threshold = 50000.00): Builder
    {
        return $query->where('amount', '>', $threshold);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Aggregation Engines (O(1) Memory Safe)
    |--------------------------------------------------------------------------
    */

    public static function totalForUser(int $userId): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->personal()
            ->sum('amount');
    }

    public static function monthlyTotal(int $userId, int $month, int $year): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->personal()
            ->forMonth($month, $year)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Data Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Safely formats the amount for UI displays without mutating the database float.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '+₹' . number_format((float) $this->amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Security Verification Helpers
    |--------------------------------------------------------------------------
    */

    public function belongsToUser(int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }
}