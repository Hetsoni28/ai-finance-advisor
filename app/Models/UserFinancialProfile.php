<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFinancialProfile extends Model
{
    protected $fillable = [
        'user_id', 'monthly_income_estimate', 'risk_tolerance',
        'investment_experience', 'age_group', 'financial_priorities',
    ];

    protected $casts = [
        'monthly_income_estimate' => 'float',
        'financial_priorities'    => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns recommended investment allocation based on risk tolerance.
     */
    public function getInvestmentAllocation(): array
    {
        return match ($this->risk_tolerance) {
            'conservative' => [
                'equity'    => 20,
                'debt'      => 40,
                'gold'      => 15,
                'liquid'    => 25,
                'label'     => 'Conservative — Capital Preservation Focus',
            ],
            'aggressive' => [
                'equity'    => 60,
                'debt'      => 15,
                'gold'      => 10,
                'liquid'    => 15,
                'label'     => 'Aggressive — High Growth Focus',
            ],
            default => [
                'equity'    => 40,
                'debt'      => 30,
                'gold'      => 15,
                'liquid'    => 15,
                'label'     => 'Moderate — Balanced Growth & Safety',
            ],
        };
    }
}
