<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->route('id'),
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'license_number' => 'required|string|max:50|unique:employees,license_number,' . $this->route('id'),
            'license_expiry' => 'required|date|after:today',
            'type' => 'required|in:driver,staff',
            'active' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('msg.name_required'),
            'email.required' => __('msg.email_required'),
            'email.email' => __('msg.email_invalid'),
            'email.unique' => __('msg.email_unique'),
            'phone.required' => __('msg.phone_required'),
            'address.required' => __('msg.address_required'),
            'license_number.required' => __('msg.license_number_required'),
            'license_number.unique' => __('msg.license_number_unique'),
            'license_expiry.required' => __('msg.license_expiry_required'),
            'license_expiry.date' => __('msg.license_expiry_date'),
            'license_expiry.after' => __('msg.license_expiry_future'),
            'type.required' => __('msg.type_required'),
            'type.in' => __('msg.type_invalid')
        ];
    }
}
