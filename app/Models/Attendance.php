<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['student_record_id', 'class_id', 'date', 'status', 'marked_by', 'marked_at', 'note'];

    protected $dates = ['date', 'marked_at', 'created_at', 'updated_at'];

    public function student_record()
    {
        return $this->belongsTo(StudentRecord::class);
    }

    public function marker()
    {
        return $this->belongsTo(\App\User::class, 'marked_by');
    }
}
