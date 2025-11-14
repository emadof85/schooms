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
    
    // New method to get employees for this salary level
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    
    // Scope for active salary levels
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Scope for salary levels by user type
    public function scopeByUserType($query, $userTypeId)
    {
        return $query->where('user_type_id', $userTypeId);
    }
}