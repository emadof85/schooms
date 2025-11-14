<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'labels',
        'type',
        'options',
        'option_labels',
        'required',
        'active',
        'sort_order',
        'entity_type'
    ];

    protected $casts = [
        'options' => 'array',
        'labels' => 'array',
        'option_labels' => 'array',
        'required' => 'boolean',
        'active' => 'boolean',
    ];

    public function fieldValues()
    {
        return $this->hasMany(FieldValue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForEntity($query, $entityType = 'student')
    {
        return $query->where('entity_type', $entityType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function getLocalizedLabelAttribute()
    {
        $currentLocale = app()->getLocale();
        $labels = $this->labels;

        if ($labels && isset($labels[$currentLocale])) {
            return $labels[$currentLocale];
        }

        // Fallback to default label
        return $this->label;
    }

    public function getLocalizedOptionsAttribute()
    {
        $currentLocale = app()->getLocale();
        $optionLabels = $this->option_labels;
        $options = $this->options;

        if (!$options) {
            return [];
        }

        $localizedOptions = [];

        foreach ($options as $index => $value) {
            // If option_labels is an array of objects like [{"ar":"value","fr":null,"ru":null}]
            if (is_array($optionLabels) && isset($optionLabels[$index]) && is_array($optionLabels[$index])) {
                $translations = $optionLabels[$index];
                if (isset($translations[$currentLocale]) && !empty($translations[$currentLocale])) {
                    $localizedOptions[$index] = $translations[$currentLocale];
                } else {
                    $localizedOptions[$index] = $value;
                }
            } else {
                // Fallback to the original option value
                $localizedOptions[$index] = $value;
            }
        }

        return $localizedOptions;
    }
}
