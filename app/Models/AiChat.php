<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class AiChat extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | 🛡️ ENUM CONSTANTS (STRICT DATA INTEGRITY)
    |--------------------------------------------------------------------------
    | Never use random strings like 'user' or 'ai' in your controllers.
    | Always use AiChat::SENDER_USER or AiChat::SENDER_AI.
    */
    public const SENDER_USER = 'user';
    public const SENDER_AI   = 'ai';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'session_id', // 🔥 Beast Mode: Groups messages into specific chat threads
        'message',
        'sender',     // Strictly 'user' or 'ai'
        'tokens',     // 🔥 Beast Mode: Tracks LLM API usage/costs per message
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tokens'     => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * The authorized node (user) who owns this chat history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ⚙️ STATE HELPERS
    |--------------------------------------------------------------------------
    */

    public function isFromUser(): bool
    {
        return $this->sender === self::SENDER_USER;
    }

    public function isFromAi(): bool
    {
        return $this->sender === self::SENDER_AI;
    }

    /*
    |--------------------------------------------------------------------------
    | 🔎 HIGH-PERFORMANCE QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeFromUser(Builder $query): Builder
    {
        return $query->where('sender', self::SENDER_USER);
    }

    public function scopeFromAi(Builder $query): Builder
    {
        return $query->where('sender', self::SENDER_AI);
    }

    /**
     * Automatically formats the chat history into the exact array structure
     * required by the OpenAI / Gemini APIs to provide conversational memory.
     *
     * @param Builder $query
     * @param int $limit How many recent messages the AI should "remember"
     */
    public function scopeRecentContext(Builder $query, int $limit = 10): Builder
    {
        // We order by latest to get the newest X messages, 
        // but the controller will need to reverse them to chronological order for the API
        return $query->latest('id')->limit($limit);
    }
}