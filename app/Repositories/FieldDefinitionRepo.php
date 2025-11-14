<?php

namespace App\Repositories;

use App\Models\FieldDefinition;

class FieldDefinitionRepo
{
    public function all()
    {
        return FieldDefinition::ordered()->get();
    }

    public function active()
    {
        return FieldDefinition::active()->ordered()->get();
    }

    public function forEntity($entityType = 'student')
    {
        return FieldDefinition::forEntity($entityType)->active()->ordered()->get();
    }

    public function create(array $data)
    {
        return FieldDefinition::create($data);
    }

    public function update($id, array $data)
    {
        $field = FieldDefinition::find($id);
        if ($field) {
            $field->update($data);
            return $field;
        }
        return null;
    }

    public function delete($id)
    {
        $field = FieldDefinition::find($id);
        if ($field) {
            return $field->delete();
        }
        return false;
    }

    public function find($id)
    {
        return FieldDefinition::find($id);
    }

    public function toggleActive($id)
    {
        $field = FieldDefinition::find($id);
        if ($field) {
            $field->update(['active' => !$field->active]);
            return $field;
        }
        return null;
    }

    public function reorder(array $order)
    {
        foreach ($order as $id => $sortOrder) {
            FieldDefinition::where('id', $id)->update(['sort_order' => $sortOrder]);
        }
        return true;
    }
}