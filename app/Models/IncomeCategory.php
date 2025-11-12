<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];
    
    public function incomeRecords()
    {
        return $this->hasMany(IncomeRecord::class);
    }
}