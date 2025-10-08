<?php

namespace App\Models;

use Eloquent;
use Spatie\Translatable\HasTranslations;

class Nationality extends Eloquent
{
    use HasTranslations; // 1. Use the trait
    
     // 2. Define which attributes are translatable
    public $translatable = ['name'];
}
