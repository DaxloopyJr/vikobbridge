<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'registration_number', 'chairman_id', 'region', 'district', 'ward', 'village', 'street',
        'region_id', 'district_id', 'ward_id', 'village_id',
        'subscription_plan_id', 'subscription_date', 'subscription_end_date', 'trial_ends_at',
        'payment_status', 'status', 'logo', 'description', 'phone_number', 'email', 'profile_completed'
    ];

    protected function casts(): array
    {
        return [
            'subscription_date' => 'datetime',
            'subscription_end_date' => 'datetime',
            'trial_ends_at' => 'datetime',
            'profile_completed' => 'boolean',
        ];
    }

    public function chairman()
    {
        return $this->belongsTo(User::class, 'chairman_id');
    }

    public function regionModel()
    {
        return $this->belongsTo(\App\Models\Location\Region::class, 'region_id');
    }

    public function districtModel()
    {
        return $this->belongsTo(\App\Models\Location\District::class, 'district_id');
    }

    public function wardModel()
    {
        return $this->belongsTo(\App\Models\Location\Ward::class, 'ward_id');
    }

    public function villageModel()
    {
        return $this->belongsTo(\App\Models\Location\Village::class, 'village_id');
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role_id', 'is_primary_group', 'joined_at', 'status')
            ->withTimestamps();
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function activeMembers()
    {
        return $this->hasMany(Member::class)->where('status', 'active');
    }

    public function collectionFunds()
    {
        return $this->hasMany(CollectionFund::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function loanTypes()
    {
        return $this->hasMany(LoanType::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function calendarYears()
    {
        return $this->hasMany(CalendarYear::class);
    }

    public function currentCalendarYear()
    {
        return $this->hasOne(CalendarYear::class)->where('is_current', true)->latest();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function expenditures()
    {
        return $this->hasMany(Expenditure::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->payment_status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isTrialExpired(): bool
    {
        return $this->payment_status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function isSubscriptionExpired(): bool
    {
        return $this->subscription_end_date && $this->subscription_end_date->isPast();
    }

    public function daysUntilExpiry(): int
    {
        if ($this->isOnTrial()) {
            return now()->diffInDays($this->trial_ends_at, false);
        }
        if ($this->subscription_end_date) {
            return now()->diffInDays($this->subscription_end_date, false);
        }
        return 0;
    }

    public function getTotalCollectionsAttribute($calendarYearId = null)
    {
        $query = $this->collections();
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('amount');
    }

    public function getTotalLoansDisbursedAttribute($calendarYearId = null)
    {
        $query = $this->loans()->where('status', 'disbursed');
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('loan_amount');
    }

    public function getTotalExpendituresAttribute($calendarYearId = null)
    {
        $query = $this->expenditures();
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('amount');
    }

    public function getLoanDefaultersCountAttribute(): int
    {
        return $this->loans()
            ->whereIn('status', ['defaulted', 'repaying'])
            ->whereHas('repayments', function ($q) {
                $q->where('payment_status', 'overdue');
            })->count();
    }

    public function getTotalDefaultedAmountAttribute(): float
    {
        return $this->loans()
            ->whereIn('status', ['defaulted', 'repaying'])
            ->sum('total_defaulted_amount');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->whereNotNull('subscription_end_date')
                    ->where('subscription_end_date', '<', now());
            });
    }
}
