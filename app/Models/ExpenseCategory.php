<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];
    
    public function expenseRecords()
    {
        return $this->hasMany(ExpenseRecord::class);
    }
}