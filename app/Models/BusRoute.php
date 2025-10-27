<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'start_location',
        'end_location',
        'description',
        'departure_time',
        'arrival_time',
        'distance_km',
        'active'
    ];

    protected $casts = [
        'departure_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
        'distance_km' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function busStops()
    {
        return $this->hasMany(BusStop::class)->orderBy('order');
    }

    public function busAssignments()
    {
        return $this->hasMany(BusAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
