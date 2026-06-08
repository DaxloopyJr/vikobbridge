<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['district', 'region_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }

    public function villages()
    {
        return $this->hasManyThrough(Village::class, Ward::class);
    }
}
