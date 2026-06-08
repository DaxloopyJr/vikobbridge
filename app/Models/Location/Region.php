<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['region', 'zone_id', 'code', 'is_deleted'];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function wards()
    {
        return $this->hasManyThrough(Ward::class, District::class);
    }

    public function villages()
    {
        return $this->hasManyThrough(Village::class, Ward::class, 'district_id', 'ward_id', 'id', 'id')
            ->through(District::class);
    }
}
