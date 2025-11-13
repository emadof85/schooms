<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdate extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $defaultLang = config('app.default_language', 'en');

        return [
            'default_language' => 'required|string|in:en,ar,fr,ru',
            'system_name_' . $defaultLang => 'required|string|min:4',
            'current_session' => 'required|string',
            'address_' . $defaultLang => 'required|string|min:15',
            'system_email' => 'sometimes|nullable|email',
            'lock_exam' => 'required',
            'logo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:2048',

            // Optional validation for other languages
            'system_name_ar' => 'sometimes|nullable|string|min:10',
            'system_name_fr' => 'sometimes|nullable|string|min:10',
            'system_name_ru' => 'sometimes|nullable|string|min:10',
            'system_title_ar' => 'sometimes|nullable|string',
            'system_title_fr' => 'sometimes|nullable|string',
            'system_title_ru' => 'sometimes|nullable|string',
            'address_ar' => 'sometimes|nullable|string|min:15',
            'address_fr' => 'sometimes|nullable|string|min:15',
            'address_ru' => 'sometimes|nullable|string|min:15',
        ];
    }

    public function attributes()
    {
        $defaultLang = config('app.default_language', 'en');

        return  [
            'default_language' => 'Default Language',
            'system_name_' . $defaultLang => 'School Name',
            'system_email' => 'School Email',
            'current_session' => 'Current Session',
            'address_' . $defaultLang => 'School Address',
            'system_name_ar' => 'School Name (Arabic)',
            'system_name_fr' => 'School Name (French)',
            'system_name_ru' => 'School Name (Russian)',
            'system_title_ar' => 'School Acronym (Arabic)',
            'system_title_fr' => 'School Acronym (French)',
            'system_title_ru' => 'School Acronym (Russian)',
            'address_ar' => 'School Address (Arabic)',
            'address_fr' => 'School Address (French)',
            'address_ru' => 'School Address (Russian)',
        ];
    }

}
