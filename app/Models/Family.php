<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Family extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description', // 🚨 CRITICAL FIX: Required for workspace creation
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 CORE RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
                User::class,
                'family_members', // Ensure this matches your actual DB table name
                'family_id',
                'user_id'
            )
            ->withPivot('role')
            ->withTimestamps();
    }

    // 🚨 CRITICAL FIX: Required for the IAM Mailbox to function
    public function invites(): HasMany
    {
        return $this->hasMany(FamilyInvite::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class)->where('is_personal', false);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class)->where('is_personal', false);
    }

    /*
    |--------------------------------------------------------------------------
    | 💰 FINANCIAL METRICS (STRICT & INDEX-OPTIMIZED)
    |--------------------------------------------------------------------------
    */

    public function totalIncome(): float
    {
        return (float) $this->incomes()->sum('amount');
    }

    public function totalExpense(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function balance(): float
    {
        return max($this->totalIncome() - $this->totalExpense(), 0.0);
    }

    public function thisMonthIncome(): float
    {
        // 🔥 BEAST MODE: whereBetween preserves MySQL indexing (Unlike whereMonth)
        $start = now()->startOfMonth()->toDateString();
        $end   = now()->endOfMonth()->toDateString();

        return (float) $this->incomes()
            ->whereBetween('income_date', [$start, $end])
            ->sum('amount');
    }

    public function thisMonthExpense(): float
    {
        $start = now()->startOfMonth()->toDateString();
        $end   = now()->endOfMonth()->toDateString();

        return (float) $this->expenses()
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | 🛡️ IDENTITY & ACCESS MANAGEMENT (RAM-OPTIMIZED)
    | Intelligently uses eager-loaded relations to prevent N+1 queries.
    |--------------------------------------------------------------------------
    */

    public function hasUser(int $userId): bool
    {
        if ($this->relationLoaded('users')) {
            return $this->users->contains('id', $userId);
        }

        return $this->users()->where('users.id', $userId)->exists();
    }

    public function member(int $userId): ?FamilyMember
    {
        if ($this->relationLoaded('members')) {
            return $this->members->firstWhere('user_id', $userId);
        }

        return $this->members()->where('user_id', $userId)->first();
    }

    public function isOwner(int $userId): bool
    {
        // Failsafe absolute creator check
        if ($this->created_by === $userId) {
            return true;
        }

        if ($this->relationLoaded('members')) {
            $member = $this->members->firstWhere('user_id', $userId);
            return $member && strtolower($member->role ?? '') === 'owner';
        }

        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'owner') // Adjust to FamilyMember::ROLE_OWNER if using constants
            ->exists();
    }

    public function isAdmin(int $userId): bool
    {
        if ($this->relationLoaded('members')) {
            $member = $this->members->firstWhere('user_id', $userId);
            return $member && strtolower($member->role ?? '') === 'admin';
        }

        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin') 
            ->exists();
    }

    public function canManage(int $userId): bool
    {
        if ($this->created_by === $userId) {
            return true;
        }

        return $this->isOwner($userId) || $this->isAdmin($userId);
    }

    public function ownerCount(): int
    {
        if ($this->relationLoaded('members')) {
            return $this->members->where('role', 'owner')->count();
        }

        return $this->members()->where('role', 'owner')->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 🔎 QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Eagerly aggregates financial totals directly in SQL.
     */
    public function scopeWithFinancials(Builder $query): Builder
    {
        return $query
            ->withSum(['incomes' => fn($q) => $q->where('is_personal', false)], 'amount')
            ->withSum(['expenses' => fn($q) => $q->where('is_personal', false)], 'amount');
    }
}