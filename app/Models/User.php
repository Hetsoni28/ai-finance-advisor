<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | ROLE CONSTANTS (NO MAGIC STRINGS)
    |--------------------------------------------------------------------------
    */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';

    /*
    |--------------------------------------------------------------------------
    | 🛡️ MASS ASSIGNMENT PROTECTION
    |--------------------------------------------------------------------------
    | `role` and `is_blocked` have been STRIPPED from fillable to prevent 
    | privilege escalation attacks during registration or profile updates.
    */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked'        => 'boolean',
        'deleted_at'        => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 CORE RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Workspaces this user is a member of (Pivot).
     */
    public function families(): BelongsToMany
    {
        return $this->belongsToMany(
            Family::class,
            'family_members',
            'user_id',
            'family_id'
        )
        ->withPivot('role')
        ->withTimestamps();
    }

    /**
     * Workspaces this user originally initialized.
     */
    public function createdFamilies(): HasMany
    {
        return $this->hasMany(Family::class, 'created_by');
    }

    /**
     * Cryptographic invites dispatched by this user.
     */
    public function dispatchedInvites(): HasMany
    {
        return $this->hasMany(FamilyInvite::class, 'created_by');
    }

    /**
     * All subscriptions for this user.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the user's currently active subscription.
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->latest('created_at');
    }

    /**
     * Get the user's current plan slug (with auto-expiry check).
     */
    public function getCurrentPlanSlugAttribute(): string
    {
        $sub = $this->activeSubscription;

        if ($sub && $sub->isExpired()) {
            $sub->update(['status' => 'expired']);
            return 'free';
        }

        return $sub?->plan?->slug ?? 'free';
    }

    /**
     * Get the user's current plan name.
     */
    public function getCurrentPlanNameAttribute(): string
    {
        $slug = $this->current_plan_slug;
        $map = ['free' => 'Starter', 'pro' => 'Pro Advisor', 'premium' => 'Enterprise'];
        return $map[$slug] ?? 'Starter';
    }

    /**
     * Check if user has a specific plan or higher.
     */
    public function hasPlan(string $minimumSlug): bool
    {
        $hierarchy = ['free' => 0, 'pro' => 1, 'premium' => 2];
        $currentLevel = $hierarchy[$this->current_plan_slug] ?? 0;
        $requiredLevel = $hierarchy[$minimumSlug] ?? 0;
        return $currentLevel >= $requiredLevel;
    }

    /**
     * The user's financial risk & investment profile.
     */
    public function financialProfile(): HasOne
    {
        return $this->hasOne(UserFinancialProfile::class);
    }

    /**
     * The user's financial goals.
     */
    public function financialGoals(): HasMany
    {
        return $this->hasMany(FinancialGoal::class);
    }

    /**
     * Compile a full financial snapshot for the AI context engine.
     */
    public function getFinancialSnapshotAttribute(): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalIncome  = (float) Income::where('user_id', $this->id)->sum('amount');
        $totalExpense = (float) Expense::where('user_id', $this->id)->sum('amount');
        $netWorth     = $totalIncome - $totalExpense;

        $monthlyIncome  = (float) Income::where('user_id', $this->id)->where('created_at', '>=', $thirtyDaysAgo)->sum('amount');
        $monthlyExpense = (float) Expense::where('user_id', $this->id)->where('created_at', '>=', $thirtyDaysAgo)->sum('amount');

        $savingRate = $totalIncome > 0 ? round(($netWorth / $totalIncome) * 100, 1) : 0;
        $runway = $monthlyExpense > 0 ? round($netWorth / $monthlyExpense, 1) : 12;

        $topCategories = Expense::where('user_id', $this->id)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'category')
            ->toArray();

        $profile = $this->financialProfile;
        $goals = $this->financialGoals()->active()->get();

        return [
            'net_worth'          => $netWorth,
            'total_income'       => $totalIncome,
            'total_expense'      => $totalExpense,
            'monthly_income'     => $monthlyIncome,
            'monthly_expense'    => $monthlyExpense,
            'saving_rate'        => $savingRate,
            'runway_months'      => $runway,
            'top_categories'     => $topCategories,
            'risk_tolerance'     => $profile->risk_tolerance ?? 'moderate',
            'investment_exp'     => $profile->investment_experience ?? 'beginner',
            'age_group'          => $profile->age_group ?? '26-35',
            'active_goals_count' => $goals->count(),
            'goals_summary'      => $goals->map(fn($g) => [
                'title'    => $g->title,
                'target'   => $g->target_amount,
                'current'  => $g->current_amount,
                'progress' => $g->progress_percent . '%',
            ])->toArray(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 📜 AUDIT TRAIL RELATIONSHIPS (SPATIE SAFE)
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieves all audit logs CAUSER by this specific user.
     * Replaces standard HasMany to prevent Spatie Polymorphic crashes.
     */
    public function actions(): MorphMany
    {
        return $this->morphMany(Activity::class, 'causer');
    }

    /*
    |--------------------------------------------------------------------------
    | ⚙️ ROLE & SECURITY HELPERS
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    /*
    |--------------------------------------------------------------------------
    | 🛡️ SECURE STATE MUTATORS
    | Use these methods in your Admin controllers instead of ->update()
    |--------------------------------------------------------------------------
    */

    public function promoteToAdmin(): void
    {
        $this->role = self::ROLE_ADMIN;
        $this->save();
    }

    public function demoteToUser(): void
    {
        $this->role = self::ROLE_USER;
        $this->save();
    }

    public function restrictNetworkAccess(): void
    {
        $this->is_blocked = true;
        $this->save();
    }

    public function restoreNetworkAccess(): void
    {
        $this->is_blocked = false;
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | 🎨 UI ACCESSORS (THE BEAST UPGRADE)
    |--------------------------------------------------------------------------
    */

    /**
     * Automatically generates a beautiful fallback avatar using the user's email.
     * Usage in Blade: <img src="{{ $user->avatar }}" />
     */
    public function getAvatarAttribute(): string
    {
        $hash = md5(strtolower(trim($this->email)));
        // Returns a sleek, geometric fallback avatar if no image is found
        return "https://www.gravatar.com/avatar/{$hash}?s=200&d=identicon";
    }

    /*
    |--------------------------------------------------------------------------
    | 🔎 QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_blocked', false);
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('is_blocked', true);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }
}