<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenditure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'calendar_year_id', 'expense_number', 'category', 'description',
        'amount', 'expense_date', 'payment_method', 'reference_number',
        'receipt_attachment', 'status', 'approved_by', 'recorded_by', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function calendarYear()
    {
        return $this->belongsTo(CalendarYear::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generateExpenseNumber(): string
    {
        $prefix = 'EXP';
        $year = date('Y');
        $random = strtoupper(substr(uniqid(), -6));
        return "{$prefix}-{$year}-{$random}";
    }
}
