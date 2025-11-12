<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeRecord extends Model
{
    protected $fillable = [
        'category_id', 'title', 'reference_no', 'amount', 'income_date',
        'payment_method', 'received_from', 'description', 'attachment', 'recorded_by'
    ];
    
    protected $casts = [
        'income_date' => 'date'
    ];
    
    public function category()
    {
        return $this->belongsTo(IncomeCategory::class);
    }
    
    public function recordedBy()
    {
        return $this->belongsTo(\App\User::class, 'recorded_by');
    }

 
}