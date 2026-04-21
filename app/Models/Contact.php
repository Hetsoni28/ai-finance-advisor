<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'location_label',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read'    => 'boolean',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔎 HIGH-PERFORMANCE QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read/archived messages.
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to order by most recent first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to search by name, email, or subject.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('subject', 'like', "%{$term}%")
              ->orWhere('message', 'like', "%{$term}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ⚙️ STATE MUTATORS (ACTIONS)
    |--------------------------------------------------------------------------
    */

    /**
     * Mark the contact message as read safely.
     * Returns true if the database update was successful.
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return true; // Already read, prevent unnecessary database queries
        }

        return $this->update(['is_read' => true]);
    }

    /**
     * Revert the contact message to unread (Admin functionality).
     * Returns true if the database update was successful.
     */
    public function markAsUnread(): bool
    {
        if (!$this->is_read) {
            return true; // Already unread
        }

        return $this->update(['is_read' => false]);
    }

    /*
    |--------------------------------------------------------------------------
    | 📐 COMPUTED ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this contact has geolocation data.
     */
    public function getHasLocationAttribute(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}