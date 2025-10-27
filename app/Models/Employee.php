<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'license_expiry',
        'type',
        'active'
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function busDrivers()
    {
        return $this->hasMany(BusDriver::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeDrivers($query)
    {
        return $query->where('type', 'driver');
    }
}
