<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'name', 'description', 'rate_type', 'rate_percentage',
        'processing_fee', 'uses_eligibility_rules', 'min_membership_months',
        'requires_active_status', 'max_loan_hisa_multiplier',
        'max_loan_term_months', 'max_loan_amount', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'rate_percentage' => 'decimal:2',
            'processing_fee' => 'decimal:2',
            'uses_eligibility_rules' => 'boolean',
            'requires_active_status' => 'boolean',
            'min_membership_months' => 'integer',
            'max_loan_hisa_multiplier' => 'decimal:2',
            'max_loan_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
