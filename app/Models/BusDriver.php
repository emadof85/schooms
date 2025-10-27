<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'bus_id',
        'assignment_date',
        'end_date',
        'active'
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->with('user');
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
