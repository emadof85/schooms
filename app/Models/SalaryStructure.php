<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'salary_level_id',
        'user_id', 
        'basic_salary',
        'housing_allowance',
        'transport_allowance', 
        'medical_allowance',
        'other_allowances',
        'total_salary',
        'effective_date',
        'is_active'
    ];
    
    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'total_salary' => 'decimal:2'
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
    
    // Helper method to get formatted salary amounts
    public function getFormattedBasicSalaryAttribute()
    {
        return '$' . number_format($this->basic_salary, 2);
    }
    
    public function getFormattedHousingAllowanceAttribute()
    {
        return '$' . number_format($this->housing_allowance, 2);
    }
    
    public function getFormattedTransportAllowanceAttribute()
    {
        return '$' . number_format($this->transport_allowance, 2);
    }
    
    public function getFormattedMedicalAllowanceAttribute()
    {
        return '$' . number_format($this->medical_allowance, 2);
    }
    
    public function getFormattedOtherAllowancesAttribute()
    {
        return '$' . number_format($this->other_allowances, 2);
    }
    
    public function getFormattedTotalSalaryAttribute()
    {
        return '$' . number_format($this->total_salary, 2);
    }
}