<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['user_id', 'category', 'monthly_limit', 'alert_level', 'alert_month'];

    protected $casts = [
        'monthly_limit' => 'float',
        'alert_level'   => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
