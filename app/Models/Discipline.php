<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'member_id', 'calendar_year_id', 'type', 'fine_type',
        'month', 'year', 'amount', 'paid_amount', 'reason', 'status',
        'payment_date', 'recorded_by', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function calendarYear()
    {
        return $this->belongsTo(CalendarYear::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getBalanceAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'skipped' => '<span class="badge bg-secondary">Skipped</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }
}
