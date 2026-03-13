<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Income extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'family_id',
        'amount',
        'source',
        'is_personal',
        'income_date',
        'month',
        'year',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'amount'      => 'float',
        'is_personal' => 'boolean',
        'income_date' => 'date',
        'month'       => 'integer',
        'year'        => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saving(function (Income $income) {

            // Personal vs Family enforcement
            if ($income->is_personal) {
                $income->family_id = null;
            }

            if (!$income->is_personal && empty($income->family_id)) {
                throw ValidationException::withMessages([
                    'family_id' => 'Family income requires a valid family.',
                ]);
            }

            if ((float) $income->amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Income amount must be greater than zero.',
                ]);
            }

            // 🔥 Auto set month/year
            if ($income->income_date) {
                $income->month = (int) $income->income_date->format('m');
                $income->year  = (int) $income->income_date->format('Y');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
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
    | Query Scopes (STRICT & SAFE)
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

    /*
    |--------------------------------------------------------------------------
    | Analytics Helpers
    |--------------------------------------------------------------------------
    */

    public static function totalForUser(int $userId): float
    {
        return (float) static::forUser($userId)
            ->personal()
            ->sum('amount');
    }

    public static function monthlyTotal(int $userId, int $month, int $year): float
    {
        return (float) static::forUser($userId)
            ->personal()
            ->forMonth($month, $year)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format((float) $this->amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Helper
    |--------------------------------------------------------------------------
    */

    public function belongsToUser(int $userId): bool
    {
        return (int) $this->user_id === (int) $userId;
    }
}