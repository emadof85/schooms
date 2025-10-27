<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'stop_name',
        'address',
        'latitude',
        'longitude',
        'order',
        'bus_route_id',
        'active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'order' => 'integer',
        'active' => 'boolean'
    ];

    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class);
    }

    public function studentBusAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
