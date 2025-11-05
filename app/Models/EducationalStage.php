<?php

namespace App\Models;

use Eloquent;

class EducationalStage extends Eloquent
{
    protected $fillable = ['name'];

    public function classes()
    {
        return $this->hasMany(MyClass::class, 'class_type_id');
    }
}
