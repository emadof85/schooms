<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'plate_number',
        'model',
        'capacity',
        'status',
        'description'
    ];

    protected $casts = [
        'capacity' => 'integer'
    ];

    public function busDrivers()
    {
        return $this->hasMany(BusDriver::class);
    }

    public function busAssignments()
    {
        return $this->hasMany(BusAssignment::class);
    }

    public function studentBusAssignments()
    {
        return $this->hasMany(StudentBusAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function currentDriver()
    {
        return $this->hasOne(BusDriver::class)->where('active', true)->with('employee');
    }
}
