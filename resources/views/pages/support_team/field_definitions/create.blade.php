@extends('layouts.master')
@section('page_title', __('msg.add_field_definition'))

@section('content')
<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">{{ __('msg.add_field_definition') }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <form method="post" action="{{ route('field-definitions.store') }}">
        @csrf
        <div class="card-body">
            <h6>{{ __('msg.field_label') }}</h6>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.english') }}</label>
                    <input type="text" name="label" class="form-control" required
                           placeholder="{{ __('msg.field_label_example') }}">
                    <small class="form-text text-muted">{{ __('msg.field_label_help') }}</small>
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.arabic') }}</label>
                    <input type="text" class="form-control" name="translated_labels[ar]"
                           placeholder="{{ __('msg.arabic') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.french') }}</label>
                    <input type="text" class="form-control" name="translated_labels[fr]"
                           placeholder="{{ __('msg.french') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-text text-muted">{{ __('msg.russian') }}</label>
                    <input type="text" class="form-control" name="translated_labels[ru]"
                           placeholder="{{ __('msg.russian') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.field_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="field-name" class="form-control" required readonly
                               placeholder="{{ __('msg.field_name_example') }}">
                        <small class="form-text text-muted">{{ __('msg.field_name_help') }}</small>
                    </div>
                </div>
            </div>
            
            <h6>{{ __('msg.field_settings') }}</h6>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-control select" required>
                            <option value="">{{ __('msg.choose') }}</option>
                            <option value="text">{{ __('msg.text') }}</option>
                            <option value="textarea">{{ __('msg.textarea') }}</option>
                            <option value="select">{{ __('msg.select') }}</option>
                            <option value="date">{{ __('msg.date') }}</option>
                            <option value="number">{{ __('msg.number') }}</option>
                            <option value="checkbox">{{ __('msg.checkbox') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.sort_order') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('msg.entity_type') }}</label>
                        <select name="entity_type" class="form-control select">
                            <option value="student">{{ __('msg.student') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="required" value="1">
                            {{ __('msg.required') }}
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="active" value="1" checked>
                            {{ __('msg.active') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="row" id="options-container" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{{ __('msg.options') }}</label>
                        <textarea name="options" class="form-control" rows="3"
                                  placeholder="{{ __('msg.options_help') }}"></textarea>
                        <small class="form-text text-muted">{{ __('msg.options_format_help') }}</small>
                    </div>
                </div>
            </div>

            <div class="row" id="option-translations-container" style="display: none;">
                <div class="col-md-12">
                    <h6>{{ __('msg.option_translations') }}</h6>
                    <div id="option-translations-list">
                        <!-- Option translation rows will be added here dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="icon-checkmark-circle2 mr-2"></i>{{ __('msg.save') }}
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

    // Handle translation input changes to sync with label
    $('input[name="translated_labels[ar]"], input[name="translated_labels[fr]"], input[name="translated_labels[ru]"]').on('input', function() {
        // Optional: could add logic here if needed
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

        let translationsHtml = '';
        availableLanguages.forEach(lang => {
            const langCode = Object.keys(lang)[0];
            const langName = lang[langCode];
            const translationValue = translations[langCode] || '';
            translationsHtml += `
                <div class="col-md-3">
                    <label class="form-text text-muted">${langName}</label>
                    <input type="text" class="form-control" name="option_translations[${optionKey}][${langCode}]"
                           value="${translationValue}" placeholder="${langName}">
                </div>
            `;
        });

        const rowHtml = `
            <div class="row mb-3" id="${rowId}">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="${keyInputId}" name="option_keys[]"
                           value="${optionKey}" placeholder="Option key" readonly>
                </div>
                ${translationsHtml}
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
            $('#option-translations-list').empty();
            Object.keys(options).forEach(key => {
                addOptionTranslationRow(key, {});
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