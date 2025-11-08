
// app/Models/SalaryRecord.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $fillable = [
        'user_id', 'payroll_period', 'payment_date', 'basic_salary',
        'total_allowances', 'gross_salary', 'total_deductions',
        'total_bonuses', 'net_salary', 'payment_method',
        'transaction_reference', 'notes', 'status', 'paid_by'
    ];
    
    protected $casts = [
        'payment_date' => 'date'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}