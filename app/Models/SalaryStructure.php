
// app/Models/SalaryStructure.php
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
        return $this->belongsTo(User::class);
    }
    
    public function calculateTotalSalary()
    {
        return $this->basic_salary + $this->housing_allowance + 
               $this->transport_allowance + $this->medical_allowance + 
               $this->other_allowances;
    }
}
