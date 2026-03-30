<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Related Models
|--------------------------------------------------------------------------
*/

use App\Models\User;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Activity Type Constants
    |--------------------------------------------------------------------------
    */

    public const TYPE_EXPENSE_CREATED  = 'expense_created';
    public const TYPE_EXPENSE_UPDATED  = 'expense_updated';
    public const TYPE_EXPENSE_DELETED  = 'expense_deleted';

    public const TYPE_INCOME_CREATED   = 'income_created';
    public const TYPE_INCOME_UPDATED   = 'income_updated';

    public const TYPE_FAMILY_CREATED   = 'family_created';

    public const TYPE_INVITE_SENT      = 'invite_sent';
    public const TYPE_INVITE_ACCEPTED  = 'invite_accepted';

    public const TYPE_ROLE_CHANGED     = 'role_changed';

    public const TYPE_LOGIN            = 'login';
    public const TYPE_LOGOUT           = 'logout';

    public const TYPE_PROFILE_UPDATED  = 'profile_updated';


    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'subject_id',
        'subject_type',
        'meta',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Activity → User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activity → Polymorphic Subject
     * (Expense, Income, Family, etc.)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }


    /*
    |--------------------------------------------------------------------------
    | Activity Logger
    |--------------------------------------------------------------------------
    */

    public static function log(
        User $user,
        string $type,
        string $description,
        ?Model $subject = null,
        array $meta = []
    ): self {

        return self::create([
            'user_id'      => $user->id,
            'type'         => $type,
            'description'  => $description,
            'subject_id'   => $subject?->id,
            'subject_type' => $subject ? get_class($subject) : null,
            'meta'         => $meta,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isType(string $type): bool
    {
        return $this->type === $type;
    }


    /*
    |--------------------------------------------------------------------------
    | Query Scopes (Admin Reports)
    |--------------------------------------------------------------------------
    */

    public function scopeRecent($query)
    {
        return $query->latest();
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}