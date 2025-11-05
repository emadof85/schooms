<?php

namespace App\Models;

use Eloquent;

class MyClass extends Eloquent
{
    protected $fillable = ['name', 'class_type_id'];

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function educational_stage()
    {
        return $this->belongsTo(EducationalStage::class, 'class_type_id');
    }

    public function student_record()
    {
        return $this->hasMany(StudentRecord::class);
    }
}
