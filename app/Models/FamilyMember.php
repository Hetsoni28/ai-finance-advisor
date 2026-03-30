<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use RuntimeException;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';

    /*
    |--------------------------------------------------------------------------
    | Role Constants (NO MAGIC STRINGS)
    |--------------------------------------------------------------------------
    */
    public const ROLE_OWNER  = 'owner';
    public const ROLE_ADMIN  = 'admin';
    public const ROLE_MEMBER = 'member';

    protected $fillable = [
        'family_id',
        'user_id',
        'role',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMember(): bool
    {
        return $this->role === self::ROLE_MEMBER;
    }

    public function canManageFamily(): bool
    {
        return in_array(
            $this->role,
            [self::ROLE_OWNER, self::ROLE_ADMIN],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */

    public function promoteToAdmin(): void
    {
        $this->update(['role' => self::ROLE_ADMIN]);
    }

    public function promoteToOwner(): void
    {
        $this->update(['role' => self::ROLE_OWNER]);
    }

    public function demoteToMember(): void
    {
        if ($this->isLastOwner()) {
            throw new RuntimeException('Security Protocol: Cannot demote the absolute final owner of a workspace.');
        }

        $this->update(['role' => self::ROLE_MEMBER]);
    }

    /*
    |--------------------------------------------------------------------------
    | Safety Protection
    |--------------------------------------------------------------------------
    */

    public function isLastOwner(): bool
    {
        if (!$this->isOwner()) {
            return false;
        }

        // If the relation is loaded or the family exists, count the owners
        if ($this->family) {
            return $this->family
                ->members()
                ->where('role', self::ROLE_OWNER)
                ->count() === 1;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 LIFECYCLE BOOT HOOKS (ENTERPRISE AUTOMATION)
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (FamilyMember $member) {
            if (!in_array($member->role, [
                self::ROLE_OWNER,
                self::ROLE_ADMIN,
                self::ROLE_MEMBER,
            ], true)) {
                throw new InvalidArgumentException("Data Integrity Error: Inherited role '{$member->role}' is completely invalid.");
            }
        });

        static::deleting(function (FamilyMember $member) {
            // Prevent deleting the last owner ONLY IF the family itself is not being actively destroyed
            if ($member->isLastOwner() && Family::where('id', $member->family_id)->exists()) {
                throw new RuntimeException('Security Protocol: Cannot remove the absolute final owner of an active workspace.');
            }
        });

        // 🔥 BEAST MODE: Autonomous Cache Invalidations
        // Instantly purges the UI Dashboard Cache so admins see role changes in real-time
        static::saved(function (FamilyMember $member) {
            Cache::forget("family_dashboard_{$member->family_id}");
        });

        static::deleted(function (FamilyMember $member) {
            Cache::forget("family_dashboard_{$member->family_id}");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOwners(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_OWNER);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeMembersOnly(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_MEMBER);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Guarantees target identity holds Owner privileges for the specified Hub.
     */
    public static function addOwner(int $familyId, int $userId): self
    {
        // 🚨 CRITICAL FIX: updateOrCreate guarantees privilege escalation
        return self::updateOrCreate(
            [
                'family_id' => $familyId,
                'user_id'   => $userId,
            ],
            [
                'role' => self::ROLE_OWNER,
            ]
        );
    }
}