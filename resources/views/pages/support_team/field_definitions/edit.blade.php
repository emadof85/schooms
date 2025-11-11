@extends('layouts.master')
@section('page_title', __('msg.edit_field_definition'))

@section('content')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">{{ __('msg.edit_field_definition') }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <form method="post" action="{{ route('field-definitions.update', $field_definition->id) }}">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.field_label') }} ({{ __('msg.default') }} - {{ __('msg.english') }}) <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" required
                               value="{{ $field_definition->label }}"
                               placeholder="{{ __('msg.field_label_example') }}">
                        <small class="form-text text-muted">{{ __('msg.field_label_help') }}</small>
                    </div>
                </div>
            </div>

            <h6>{{ __('msg.translations') }}</h6>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.english') }}</label>
                    <input type="text" name="label" class="form-control" required
                           value="{{ $field_definition->label }}"
                           placeholder="{{ __('msg.field_label_example') }}">
                    <small class="form-text text-muted">{{ __('msg.field_label_help') }}</small>
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.arabic') }}</label>
                    <input type="text" class="form-control" name="translated_labels[ar]"
                           value="{{ $field_definition->labels['ar'] ?? '' }}"
                           placeholder="{{ __('msg.arabic') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.french') }}</label>
                    <input type="text" class="form-control" name="translated_labels[fr]"
                           value="{{ $field_definition->labels['fr'] ?? '' }}"
                           placeholder="{{ __('msg.french') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.russian') }}</label>
                    <input type="text" class="form-control" name="translated_labels[ru]"
                           value="{{ $field_definition->labels['ru'] ?? '' }}"
                           placeholder="{{ __('msg.russian') }}">
                </div>
            </div>

            <h6>{{ __('msg.field_settings') }}</h6>
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.field_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="field-name" class="form-control" required readonly
                               value="{{ $field_definition->name }}"
                               placeholder="{{ __('msg.field_name_example') }}">
                        <small class="form-text text-muted">{{ __('msg.field_name_help') }}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.field_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="field-name" class="form-control" required readonly
                               value="{{ $field_definition->name }}"
                               placeholder="{{ __('msg.field_name_example') }}">
                        <small class="form-text text-muted">{{ __('msg.field_name_help') }}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-control select" required>
                            <option value="">{{ __('msg.choose') }}</option>
                            <option value="text" {{ $field_definition->type == 'text' ? 'selected' : '' }}>{{ __('msg.text') }}</option>
                            <option value="textarea" {{ $field_definition->type == 'textarea' ? 'selected' : '' }}>{{ __('msg.textarea') }}</option>
                            <option value="select" {{ $field_definition->type == 'select' ? 'selected' : '' }}>{{ __('msg.select') }}</option>
                            <option value="date" {{ $field_definition->type == 'date' ? 'selected' : '' }}>{{ __('msg.date') }}</option>
                            <option value="number" {{ $field_definition->type == 'number' ? 'selected' : '' }}>{{ __('msg.number') }}</option>
                            <option value="checkbox" {{ $field_definition->type == 'checkbox' ? 'selected' : '' }}>{{ __('msg.checkbox') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.sort_order') }}</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="{{ $field_definition->sort_order }}" min="0">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.entity_type') }}</label>
                        <select name="entity_type" class="form-control select">
                            <option value="student" {{ $field_definition->entity_type == 'student' ? 'selected' : '' }}>{{ __('msg.student') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="required" value="1"
                                   {{ $field_definition->required ? 'checked' : '' }}>
                            {{ __('msg.required') }}
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="active" value="1"
                                   {{ $field_definition->active ? 'checked' : '' }}>
                            {{ __('msg.active') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="row" id="options-container" style="{{ $field_definition->type == 'select' ? '' : 'display: none;' }}">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ __('msg.options') }}</label>
                        <textarea name="options" class="form-control" rows="3"
                                  placeholder="{{ __('msg.options_help') }}">{{ is_array($field_definition->options) ? json_encode($field_definition->options, JSON_PRETTY_PRINT) : $field_definition->options }}</textarea>
                        <small class="form-text text-muted">{{ __('msg.options_format_help') }}</small>
                    </div>
                </div>
            </div>

            <div class="row" id="option-translations-container" style="{{ $field_definition->type == 'select' ? '' : 'display: none;' }}">
                <div class="col-md-12">
                    <h6>{{ __('msg.option_translations') }}</h6>
                    <div id="option-translations-list">
                        @if($field_definition->option_labels)
                            @foreach($field_definition->option_labels as $optionKey => $translations)
                                <div class="row mb-3" id="option-translation-row-{{ $optionKey }}">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="option-key-{{ $optionKey }}" name="option_keys[]"
                                               value="{{ $optionKey }}" placeholder="Option key" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-text text-muted">{{ __('msg.arabic') }}</label>
                                        <input type="text" class="form-control" name="option_translations[{{ $optionKey }}][ar]"
                                               value="{{ $translations['ar'] ?? '' }}" placeholder="{{ __('msg.arabic') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-text text-muted">{{ __('msg.french') }}</label>
                                        <input type="text" class="form-control" name="option_translations[{{ $optionKey }}][fr]"
                                               value="{{ $translations['fr'] ?? '' }}" placeholder="{{ __('msg.french') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-text text-muted">{{ __('msg.russian') }}</label>
                                        <input type="text" class="form-control" name="option_translations[{{ $optionKey }}][ru]"
                                               value="{{ $translations['ru'] ?? '' }}" placeholder="{{ __('msg.russian') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-option-translation"
                                                data-row-id="option-translation-row-{{ $optionKey }}" data-option-key="{{ $optionKey }}">
                                            <i class="icon-cross2"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="icon-checkmark-circle2 mr-2"></i>{{ __('msg.update') }}
            </button>
            <a href="{{ route('field-definitions.index') }}" class="btn btn-light">
                {{ __('msg.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Available languages with translations (excluding English as it's the default)
    const availableLanguages = [
        {'ar': '{{ __("msg.arabic") }}'},
        {'fr': '{{ __("msg.french") }}'},
        {'ru': '{{ __("msg.russian") }}'}
    ];

    // Function to generate field name from label
    function generateFieldName(label) {
        // Convert label to snake_case and remove special characters
        let name = label.replace(/[^a-zA-Z0-9\s]/g, '');
        name = name.replace(/\s+/g, '_');
        name = name.toLowerCase();
        return name;
    }

    // Handle label input change
    $('input[name="label"]').on('input', function() {
        const label = $(this).val();
        const generatedName = generateFieldName(label);
        $('#field-name').val(generatedName);
    });

    // Handle field type change
    $('select[name="type"]').on('change', function() {
        if ($(this).val() === 'select') {
            $('#options-container').show();
            $('#option-translations-container').show();
        } else {
            $('#options-container').hide();
            $('#option-translations-container').hide();
        }
    });

    // Function to add option translation row
    function addOptionTranslationRow(optionKey = '', translations = {}) {
        const rowId = 'option-translation-row-' + optionKey;
        const keyInputId = 'option-key-' + optionKey;

        const rowHtml = `
            <div class="row mb-3" id="${rowId}">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="${keyInputId}" name="option_keys[]"
                           value="${optionKey}" placeholder="Option key" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.arabic') }}</label>
                    <input type="text" class="form-control" name="option_translations[${optionKey}][ar]"
                           value="${translations.ar || ''}" placeholder="{{ __('msg.arabic') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.french') }}</label>
                    <input type="text" class="form-control" name="option_translations[${optionKey}][fr]"
                           value="${translations.fr || ''}" placeholder="{{ __('msg.french') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.russian') }}</label>
                    <input type="text" class="form-control" name="option_translations[${optionKey}][ru]"
                           value="${translations.ru || ''}" placeholder="{{ __('msg.russian') }}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-option-translation"
                            data-row-id="${rowId}" data-option-key="${optionKey}">
                        <i class="icon-cross2"></i>
                    </button>
                </div>
            </div>
        `;

        $('#option-translations-list').append(rowHtml);
    }

    // Handle options textarea change
    $('textarea[name="options"]').on('input', function() {
        const optionsText = $(this).val();
        try {
            const options = JSON.parse(optionsText);
            const existingKeys = new Set();
            $('#option-translations-list .row').each(function() {
                const key = $(this).find('input[name="option_keys[]"]').val();
                existingKeys.add(key);
            });

            Object.keys(options).forEach(key => {
                if (!existingKeys.has(key)) {
                    addOptionTranslationRow(key, {});
                }
            });
        } catch (e) {
            // Invalid JSON, do nothing
        }
    });

    // Remove option translation row
    $(document).on('click', '.remove-option-translation', function() {
        const rowId = $(this).data('row-id');
        $('#' + rowId).remove();
    });

    // No initialization needed for fixed layout
});
</script>
@endsection