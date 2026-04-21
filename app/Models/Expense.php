<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment Security
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'family_id',
        'title',
        'category',
        'amount',
        'expense_date',
        'is_personal',
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
        'expense_date' => 'date',
        'is_personal'  => 'boolean',
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
        static::saving(function (Expense $expense) {

            // 1. Core Ownership Guard
            if (empty($expense->user_id)) {
                throw new InvalidArgumentException('Data Integrity Violation: Expense must belong to a valid user_id.');
            }

            // 2. Personal vs Family Guard
            if ($expense->is_personal) {
                // Force null to prevent mixed-state data corruption
                $expense->family_id = null; 
            }

            if (!$expense->is_personal && empty($expense->family_id)) {
                throw new InvalidArgumentException('Data Integrity Violation: Corporate/Family expense requires a valid family_id.');
            }

            // 3. Automated Chronology Indexing (Fixes Carbon::parse(null) bug)
            if (empty($expense->expense_date)) {
                throw new InvalidArgumentException('Data Integrity Violation: expense_date cannot be null.');
            }

            $date = $expense->expense_date instanceof Carbon
                ? $expense->expense_date
                : Carbon::parse($expense->expense_date);

            $expense->month = $date->month;
            $expense->year  = $date->year;
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
            ->orderByDesc('expense_date')
            ->orderByDesc('id');
    }

    /**
     * Identifies high-value transactions for the Security Dashboard.
     */
    public function scopeAnomalous(Builder $query, float $threshold = 10000.00): Builder
    {
        return $query->where('amount', '>', $threshold);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Aggregation Engines (O(1) Memory Safe)
    |--------------------------------------------------------------------------
    */

    public static function personalTotal(int $userId): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->personal()
            ->sum('amount');
    }

    public static function familyTotal(int $familyId): float
    {
        return (float) static::query()
            ->forFamily($familyId)
            ->sum('amount');
    }

    public static function monthlyPersonalTotal(int $userId, int $month, int $year): float 
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
        return '-₹' . number_format((float) $this->amount, 2);
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