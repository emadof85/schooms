<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\FieldDefinition;
use App\Repositories\FieldDefinitionRepo;
use Illuminate\Http\Request;

class FieldDefinitionController extends Controller
{
    protected $fieldDefinition;

    public function __construct(FieldDefinitionRepo $fieldDefinition)
    {
        $this->middleware('teamSA');
        $this->fieldDefinition = $fieldDefinition;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['field_definitions'] = $this->fieldDefinition->all();
        return view('pages.support_team.field_definitions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.support_team.field_definitions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string',
            'type' => 'required|in:text,textarea,select,date,number,checkbox',
            'required' => 'boolean',
            'active' => 'boolean',
        ]);

        $data = $request->only(['label', 'type', 'required', 'active', 'entity_type']);
        $data['entity_type'] = $data['entity_type'] ?? 'student';
        $data['name'] = $this->generateUniqueName($request->label);

        // Handle labels in multiple languages
        $labels = [];
        $translatedLabels = $request->input('translated_labels', []);

        // Set English as default
        $labels['en'] = $request->label;

        // Add other language translations
        $supportedLocales = ['ar', 'fr', 'ru'];
        foreach ($supportedLocales as $locale) {
            if (!empty($translatedLabels[$locale])) {
                $labels[$locale] = $translatedLabels[$locale];
            } else {
                // Fallback to default label if translation is empty
                $labels[$locale] = $request->label;
            }
        }

        $data['labels'] = $labels;
        $data['options'] = $request->has('options') && !empty($request->options) ? json_decode($request->options, true) : null;

        // Handle option translations
        $optionLabels = [];
        $optionTranslations = $request->input('option_translations', []);
        $optionKeys = $request->input('option_keys', []);

        foreach ($optionKeys as $key) {
            if (isset($optionTranslations[$key])) {
                $optionLabels[$key] = $optionTranslations[$key];
            }
        }

        $data['option_labels'] = $optionLabels;

        $this->fieldDefinition->create($data);

        return redirect()->route('field-definitions.index')->with('flash_success', __('msg.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['field_definition'] = $this->fieldDefinition->find($id);
        if (!$data['field_definition']) {
            return Qs::goWithDanger();
        }
        return view('pages.support_team.field_definitions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string',
            'type' => 'required|in:text,textarea,select,date,number,checkbox',
            'required' => 'boolean',
            'active' => 'boolean',
        ]);

        $data = $request->only(['label', 'type', 'required', 'active', 'entity_type']);
        $data['entity_type'] = $data['entity_type'] ?? 'student';

        // Only regenerate name if label has changed
        $field = $this->fieldDefinition->find($id);
        if ($field && $field->label !== $request->label) {
            $data['name'] = $this->generateUniqueName($request->label, $id);
        }

        // Handle labels in multiple languages
        $labels = [];
        $translatedLabels = $request->input('translated_labels', []);

        // Set English as default
        $labels['en'] = $request->label;

        // Add other language translations
        $supportedLocales = ['ar', 'fr', 'ru'];
        foreach ($supportedLocales as $locale) {
            if (!empty($translatedLabels[$locale])) {
                $labels[$locale] = $translatedLabels[$locale];
            } else {
                // Fallback to default label if translation is empty
                $labels[$locale] = $request->label;
            }
        }

        $data['labels'] = $labels;
        $data['options'] = $request->has('options') && !empty($request->options) ? json_decode($request->options, true) : null;

        // Handle option translations
        $optionLabels = [];
        $optionTranslations = $request->input('option_translations', []);
        $optionKeys = $request->input('option_keys', []);

        foreach ($optionKeys as $key) {
            if (isset($optionTranslations[$key])) {
                $optionLabels[$key] = $optionTranslations[$key];
            }
        }

        $data['option_labels'] = $optionLabels;

        $this->fieldDefinition->update($id, $data);

        return redirect()->route('field-definitions.index')->with('flash_success', __('msg.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->fieldDefinition->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

    public function toggle($field_definition)
    {
        $field = $this->fieldDefinition->toggleActive($field_definition);
        if (!$field) {
            return Qs::goWithDanger();
        }
        $message = $field->active ? __('msg.activated') : __('msg.deactivated');
        return back()->with('flash_success', $message);
    }

    private function generateUniqueName($label, $excludeId = null)
    {
        // Convert label to snake_case and remove special characters
        $name = preg_replace('/[^a-zA-Z0-9\s]/', '', $label);
        $name = preg_replace('/\s+/', '_', $name);
        $name = strtolower($name);

        // Ensure uniqueness
        $originalName = $name;
        $counter = 1;

        while (true) {
            $query = FieldDefinition::where('name', $name);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $name = $originalName . '_' . $counter;
            $counter++;
        }

        return $name;
    }
}
