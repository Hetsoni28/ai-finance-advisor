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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read'    => 'boolean',
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
}