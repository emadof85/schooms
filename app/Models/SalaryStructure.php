<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'salary_level_id', 'user_id', 'basic_salary', 'housing_allowance',
        'transport_allowance', 'medical_allowance', 'other_allowances',
        'total_salary', 'effective_date', 'is_active'
    ];
    
    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean'
    ];
    
    public function salaryLevel()
    {
        return $this->belongsTo(SalaryLevel::class);
    }
    
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
    
    public function calculateTotalSalary()
    {
        return $this->basic_salary + 
               ($this->housing_allowance ?? 0) + 
               ($this->transport_allowance ?? 0) + 
               ($this->medical_allowance ?? 0) + 
               ($this->other_allowances ?? 0);
    }
}