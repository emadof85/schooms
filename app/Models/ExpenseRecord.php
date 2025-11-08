// app/Models/ExpenseRecord.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseRecord extends Model
{
    protected $fillable = [
        'category_id', 'title', 'reference_no', 'amount', 'expense_date',
        'payment_method', 'paid_to', 'description', 'attachment', 'recorded_by'
    ];
    
    protected $casts = [
        'expense_date' => 'date'
    ];
    
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
    
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}