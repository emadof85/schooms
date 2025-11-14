<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_definition_id',
        'entity_id',
        'entity_type',
        'value'
    ];

    public function fieldDefinition()
    {
        return $this->belongsTo(FieldDefinition::class);
    }

    public function scopeForEntity($query, $entityId, $entityType = 'student_record')
    {
        return $query->where('entity_id', $entityId)->where('entity_type', $entityType);
    }

    public function scopeForField($query, $fieldDefinitionId)
    {
        return $query->where('field_definition_id', $fieldDefinitionId);
    }
}
