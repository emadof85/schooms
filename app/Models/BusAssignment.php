<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'bus_route_id',
        'assignment_date',
        'end_date',
        'active'
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
