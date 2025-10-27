<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBusAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_record_id',
        'bus_assignment_id',
        'bus_stop_id',
        'fee',
        'status'
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'status' => 'string'
    ];

    public function student()
    {
        return $this->belongsTo(StudentRecord::class, 'student_record_id');
    }

    public function busAssignment()
    {
        return $this->belongsTo(BusAssignment::class);
    }

    public function busStop()
    {
        return $this->belongsTo(BusStop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
