<?php

namespace App\Models;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRecord extends Eloquent
{
    use HasFactory;

    protected $fillable = [
        'session', 'user_id', 'my_class_id', 'section_id', 'my_parent_id', 'dorm_id', 'dorm_room_no', 'adm_no', 'year_admitted', 'wd', 'wd_date', 'grad', 'grad_date', 'house', 'age'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function my_parent()
    {
        return $this->belongsTo(User::class);
    }

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function dorm()
    {
        return $this->belongsTo(Dorm::class);
    }

    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : 'N/A';
    }

    public function fieldValues()
    {
        return $this->morphMany(FieldValue::class, 'entity');
    }

    public function getDynamicFieldValue($fieldName)
    {
        $fieldValue = $this->fieldValues()->whereHas('fieldDefinition', function($q) use ($fieldName) {
            $q->where('name', $fieldName)->active();
        })->first();

        return $fieldValue ? $fieldValue->value : null;
    }

    public function setDynamicFieldValue($fieldName, $value)
    {
        $fieldDefinition = FieldDefinition::where('name', $fieldName)->active()->first();

        if (!$fieldDefinition) {
            return false;
        }

        return $this->fieldValues()->updateOrCreate(
            ['field_definition_id' => $fieldDefinition->id],
            ['value' => $value]
        );
    }

    public function getAllDynamicFields()
    {
        return $this->fieldValues()->with('fieldDefinition')->get()->keyBy(function($item) {
            return $item->fieldDefinition->name;
        });
    }
}
