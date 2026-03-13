<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
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
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'amount'       => 'float',        // 🔥 safer for calculations
        'expense_date' => 'date',
        'is_personal'  => 'boolean',
        'month'        => 'integer',
        'year'         => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saving(function (Expense $expense) {

            if (!$expense->user_id) {
                throw ValidationException::withMessages([
                    'user_id' => 'Expense must belong to a valid user.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Personal vs Family Guard
            |--------------------------------------------------------------------------
            */

            if ($expense->is_personal) {
                $expense->family_id = null;
            }

            if (!$expense->is_personal && empty($expense->family_id)) {
                throw ValidationException::withMessages([
                    'family_id' => 'Family expense requires a valid family.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Auto Month/Year From Date
            |--------------------------------------------------------------------------
            */

            $date = $expense->expense_date instanceof Carbon
                ? $expense->expense_date
                : Carbon::parse($expense->expense_date);

            $expense->month = $date->month;
            $expense->year  = $date->year;
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
    | Scopes (STRICT SEPARATION)
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

    /*
    |--------------------------------------------------------------------------
    | Aggregation Helpers (SAFE)
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

    public static function monthlyPersonalTotal(
        int $userId,
        int $month,
        int $year
    ): float {
        return (float) static::query()
            ->where('user_id', $userId)
            ->personal()
            ->forMonth($month, $year)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return '-₹' . number_format($this->amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Security Helper
    |--------------------------------------------------------------------------
    */

    public function belongsToUser(int $userId): bool
    {
        return (int) $this->user_id === (int) $userId;
    }
}