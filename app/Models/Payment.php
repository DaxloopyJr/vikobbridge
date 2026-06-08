<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'subscription_plan_id', 'transaction_id', 'selcom_transaction_id',
        'order_id', 'amount', 'currency', 'payment_method', 'payment_channel',
        'phone_number', 'control_number', 'status', 'paid_at', 'payment_response',
        'failure_reason', 'is_renewal'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'payment_response' => 'array',
            'is_renewal' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $group = $this->group;
        $plan = $this->subscriptionPlan;
        
        $group->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'subscription_end_date' => now()->addDays($plan->duration_days),
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    public static function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(uniqid() . bin2hex(random_bytes(4)));
    }
}
