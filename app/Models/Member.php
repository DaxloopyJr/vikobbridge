<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'user_id', 'member_number', 'first_name', 'middle_name', 'last_name',
        'gender', 'phone_number', 'email', 'profile_picture', 'region', 'district', 'ward',
        'village', 'street', 'join_date', 'cell_leader_name', 'cell_leader_phone',
        'lg_chairperson_name', 'lg_chairperson_phone', 'guarantor_name', 'guarantor_phone',
        'guarantor_relationship', 'marital_status', 'spouse_name', 'spouse_phone',
        'spouse_occupation', 'dependents', 'inheritors', 'status', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'dependents' => 'array',
            'inheritors' => 'array',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->whereIn('status', ['disbursed', 'repaying']);
    }

    public function loanRepayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getTotalCollectionsAttribute($calendarYearId = null): float
    {
        $query = $this->collections();
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('amount');
    }

    public function getTotalCollectionsByFundAttribute($fundId, $calendarYearId = null): float
    {
        $query = $this->collections()->where('collection_fund_id', $fundId);
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('amount');
    }

    public function getTotalLoansAttribute($calendarYearId = null): float
    {
        $query = $this->loans()->whereIn('status', ['disbursed', 'repaying', 'completed', 'defaulted']);
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('loan_amount');
    }

    public function getDividendAttribute($totalProfit, $totalShares): float
    {
        if ($totalShares <= 0) return 0;
        $memberShares = $this->getTotalCollectionsByFundAttribute(
            CollectionFund::where('slug', 'hisa')->where('group_id', $this->group_id)->value('id') ?? 0
        );
        return ($memberShares / $totalShares) * $totalProfit;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
