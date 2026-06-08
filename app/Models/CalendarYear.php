<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'name', 'year', 'start_date', 'end_date', 'status', 'is_current'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function expenditures()
    {
        return $this->hasMany(Expenditure::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function setAsCurrent()
    {
        $this->group->calendarYears()->update(['is_current' => false]);
        $this->update(['is_current' => true, 'status' => 'active']);
    }
}
