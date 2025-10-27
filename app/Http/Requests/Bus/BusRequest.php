<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;

class BusRequest extends FormRequest
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
            'bus_number' => 'required|string|max:50|unique:buses,bus_number,' . $this->route('bus'),
            'plate_number' => 'required|string|max:20|unique:buses,plate_number,' . $this->route('bus'),
            'model' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string|max:500'
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
            'bus_number.required' => __('msg.bus_number_required'),
            'bus_number.unique' => __('msg.bus_number_unique'),
            'plate_number.required' => __('msg.plate_number_required'),
            'plate_number.unique' => __('msg.plate_number_unique'),
            'model.required' => __('msg.model_required'),
            'capacity.required' => __('msg.capacity_required'),
            'capacity.integer' => __('msg.capacity_integer'),
            'capacity.min' => __('msg.capacity_min'),
            'capacity.max' => __('msg.capacity_max'),
            'status.required' => __('msg.status_required'),
            'status.in' => __('msg.status_invalid'),
            'description.max' => __('msg.description_max')
        ];
    }
}
