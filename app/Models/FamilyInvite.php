<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

class FamilyInvite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'family_id',
        'email',
        'token',
        'expires_at',
        'accepted_at',
        'accepted_by',
        'created_by', // 🚨 CRITICAL FIX: Required for tracking and rate limiting
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Prevents active cryptographic tokens from leaking in JSON/API responses.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at'  => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * The user who successfully consumed the token.
     */
    public function acceptedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * The authorized node (admin/owner) who dispatched the invite.
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔎 QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->pending()
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        // 🚨 CRITICAL FIX: Ensure we only target pending invites. 
        // We do not want to delete historical logs of accepted invites!
        return $query
            ->pending()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /*
    |--------------------------------------------------------------------------
    | ⚙️ BUSINESS LOGIC & STATE VALIDATION
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expires_at instanceof Carbon
            && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Consumes the cryptographic token.
     *
     * @param int $userId
     * @throws RuntimeException
     */
    public function accept(int $userId): void
    {
        if ($this->isExpired()) {
            throw new RuntimeException('Security Protocol: This transmission token has expired.');
        }

        if ($this->isAccepted()) {
            throw new RuntimeException('Security Protocol: This transmission token has already been consumed.');
        }

        $this->update([
            'accepted_at' => now(),
            'accepted_by' => $userId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 🛡️ CRYPTOGRAPHY & FACTORY HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Generates a mathematically secure HMAC token.
     */
    public static function generateToken(): string
    {
        $rawString = Str::random(64);
        return hash_hmac('sha256', $rawString, config('app.key'));
    }

    /*
    |--------------------------------------------------------------------------
    | 🚀 BOOT HOOKS
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (FamilyInvite $invite) {

            // Guarantee a token exists and is highly secure
            if (empty($invite->token)) {
                $invite->token = self::generateToken();
            }

            // Enterprise standard: 7-day TTL (Time To Live)
            if (empty($invite->expires_at)) {
                $invite->expires_at = now()->addDays(7);
            }
        });
    }
}