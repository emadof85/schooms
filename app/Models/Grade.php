<?php

namespace App\Models;

use Eloquent;

class Grade extends Eloquent
{
    protected $fillable = ['name', 'educational_stage_id', 'mark_from', 'mark_to', 'remark'];
}
