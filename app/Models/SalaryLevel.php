<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryLevel extends Model
{
    protected $fillable = ['name', 'user_type_id', 'base_salary', 'description', 'is_active'];
    
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }
    
    public function salaryStructures()
    {
        return $this->hasMany(SalaryStructure::class);
    }
}