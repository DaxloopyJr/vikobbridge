<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class LoanRepayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id', 'member_id', 'installment_number', 'due_date', 'emi_amount',
        'principal_amount', 'interest_amount', 'balance_amount', 'amount_paid',
        'penalty_amount', 'payment_date', 'payment_status', 'payment_channel',
        'transaction_reference', 'notes', 'recorded_by'
    ];

    protected function casts(): array
    {
        return [
            'emi_amount' => 'decimal:2',
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'due_date' => 'date',
            'payment_date' => 'date',
        ];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'pending' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) return 0;
        return $this->due_date->diffInDays(now());
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->emi_amount + $this->penalty_amount - $this->amount_paid);
    }

    public function markAsPaid(float $amount = null, string $channel = 'cash', string $reference = null): void
    {
        $this->update([
            'amount_paid' => $amount ?? $this->emi_amount,
            'payment_date' => now(),
            'payment_status' => 'paid',
            'payment_channel' => $channel,
            'transaction_reference' => $reference,
        ]);

        $this->updateLoanStatus();
    }

    public function markAsSkipped(): void
    {
        $this->update([
            'payment_status' => 'skipped',
            'penalty_amount' => $this->penalty_amount + ($this->emi_amount * 0.05),
        ]);

        $this->loan->increment('total_defaulted_amount', $this->emi_amount);
        $this->updateLoanStatus();
    }

    private function updateLoanStatus(): void
    {
        $loan = $this->loan;
        $totalPaid = $loan->repayments()->sum('amount_paid');
        $loan->update([
            'amount_paid' => $totalPaid,
            'amount_remaining' => max(0, $loan->total_repayment_amount - $totalPaid),
        ]);

        if ($loan->amount_remaining <= 0) {
            $loan->update(['status' => 'completed']);
        } elseif ($loan->repayments()->where('payment_status', 'overdue')->count() > 2) {
            $loan->update(['status' => 'defaulted']);
        }
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'pending')
            ->where('due_date', '<', now());
    }
}
