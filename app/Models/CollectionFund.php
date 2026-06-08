<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollectionFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id', 'name', 'slug', 'description', 'fund_type', 'is_mandatory',
        'default_amount', 'is_active', 'display_order'
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'default_amount' => 'decimal:2',
            'is_active' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalCollectionsAttribute($calendarYearId = null): float
    {
        $query = $this->collections();
        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }
        return $query->sum('amount');
    }
}
