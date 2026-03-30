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