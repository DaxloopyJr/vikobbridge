<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'member_id', 'loan_type_id', 'calendar_year_id', 'loan_number',
        'loan_amount', 'interest_rate', 'rate_type', 'loan_term_months',
        'total_interest_amount', 'total_repayment_amount', 'monthly_installment',
        'amount_paid', 'amount_remaining', 'total_defaulted_amount',
        'application_date', 'disbursement_date', 'first_installment_date',
        'reason', 'status', 'source', 'cosigners_approved',
        'approved_by', 'approved_at', 'approval_notes',
        'treasurer_approved_by', 'treasurer_approved_at',
        'secretary_approved_by', 'secretary_approved_at',
        'chairman_approved_by', 'chairman_approved_at',
        'disbursed_by', 'disbursed_at', 'disbursement_notes'
    ];

    protected function casts(): array
    {
        return [
            'loan_amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'total_interest_amount' => 'decimal:2',
            'total_repayment_amount' => 'decimal:2',
            'monthly_installment' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_remaining' => 'decimal:2',
            'total_defaulted_amount' => 'decimal:2',
            'application_date' => 'date',
            'disbursement_date' => 'date',
            'first_installment_date' => 'date',
            'approved_at' => 'datetime',
            'treasurer_approved_at' => 'datetime',
            'secretary_approved_at' => 'datetime',
            'chairman_approved_at' => 'datetime',
            'disbursed_at' => 'datetime',
            'cosigners_approved' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function calendarYear()
    {
        return $this->belongsTo(CalendarYear::class);
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class)->orderBy('installment_number');
    }

    public function pendingRepayments()
    {
        return $this->hasMany(LoanRepayment::class)->whereIn('payment_status', ['pending', 'overdue']);
    }

    public function overdueRepayments()
    {
        return $this->hasMany(LoanRepayment::class)->where('payment_status', 'overdue');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function disburser()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function cosigners()
    {
        return $this->hasMany(LoanCosigner::class);
    }

    public function collaterals()
    {
        return $this->hasMany(LoanCollateral::class);
    }

    public function treasurerApprover()
    {
        return $this->belongsTo(User::class, 'treasurer_approved_by');
    }

    public function secretaryApprover()
    {
        return $this->belongsTo(User::class, 'secretary_approved_by');
    }

    public function chairmanApprover()
    {
        return $this->belongsTo(User::class, 'chairman_approved_by');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_repayment_amount <= 0) return 0;
        return min(100, ($this->amount_paid / $this->total_repayment_amount) * 100);
    }

    public function getPaidInstallmentsCountAttribute(): int
    {
        return $this->repayments()->where('payment_status', 'paid')->count();
    }

    public function getTotalInstallmentsCountAttribute(): int
    {
        return $this->repayments()->count();
    }

    public function getNextDueDateAttribute(): ?string
    {
        $nextPayment = $this->pendingRepayments()->orderBy('due_date')->first();
        return $nextPayment?->due_date;
    }

    public function isFullyPaid(): bool
    {
        return $this->amount_remaining <= 0;
    }

    public function isInDefault(): bool
    {
        return $this->overdueRepayments()->count() > 0;
    }

    /** All cosigners have approved */
    public function allCosignersApproved(): bool
    {
        $total = $this->cosigners()->count();
        if ($total === 0) return true; // no cosigners required
        return $this->cosigners()->where('approved', true)->count() === $total;
    }

    /** At least one cosigner rejected */
    public function anyCosignerRejected(): bool
    {
        return $this->cosigners()->where('rejected', true)->exists();
    }

    /** Treasurer has approved */
    public function treasurerApproved(): bool
    {
        return !is_null($this->treasurer_approved_by);
    }

    /** Secretary has approved */
    public function secretaryApproved(): bool
    {
        return !is_null($this->secretary_approved_by);
    }

    /** Chairman has approved */
    public function chairmanApproved(): bool
    {
        return !is_null($this->chairman_approved_by);
    }

    /** All officer approvals complete */
    public function allOfficersApproved(): bool
    {
        return $this->treasurerApproved() && $this->secretaryApproved() && $this->chairmanApproved();
    }

    /** Ready for disbursement */
    public function readyForDisbursement(): bool
    {
        return $this->allCosignersApproved() && $this->allOfficersApproved();
    }

    /** Get current approval stage label */
    public function approvalStageLabel(): string
    {
        if ($this->anyCosignerRejected()) return 'Rejected by Cosigner';
        if (!$this->allCosignersApproved()) return 'Awaiting Cosigner Approval';
        if (!$this->treasurerApproved()) return 'Awaiting Treasurer Approval';
        if (!$this->secretaryApproved()) return 'Awaiting Secretary Approval';
        if (!$this->chairmanApproved()) return 'Awaiting Chairman Approval';
        return 'Fully Approved';
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['disbursed', 'repaying']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDefaulted($query)
    {
        return $query->where('status', 'defaulted');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByCalendarYear($query, $calendarYearId)
    {
        return $query->where('calendar_year_id', $calendarYearId);
    }

    public static function generateLoanNumber(): string
    {
        $prefix = 'LN';
        $year = date('Y');
        $random = strtoupper(substr(uniqid(), -6));
        return "{$prefix}-{$year}-{$random}";
    }
}
