// app/Models/DeductionBonus.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductionBonus extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'amount', 'calculation_type',
        'description', 'effective_date', 'end_date', 'is_recurring',
        'month_year', 'applied'
    ];
    
    protected $casts = [
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'applied' => 'boolean'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}