<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'member_id', 'collection_fund_id', 'calendar_year_id',
        'amount', 'payment_date', 'month', 'year', 'is_new_calendar_year', 'balance_carried_forward',
        'payment_channel', 'payment_control_number',
        'transaction_reference', 'source', 'notes', 'recorded_by'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
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

    public function collectionFund()
    {
        return $this->belongsTo(CollectionFund::class);
    }

    public function calendarYear()
    {
        return $this->belongsTo(CalendarYear::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
