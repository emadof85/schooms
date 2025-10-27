<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;

class BusRouteRequest extends FormRequest
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
            'route_name' => 'required|string|max:255|unique:bus_routes,route_name,' . $this->route('id'),
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'distance_km' => 'required|numeric|min:0|max:999.99',
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
            'route_name.required' => __('msg.route_name_required'),
            'route_name.unique' => __('msg.route_name_unique'),
            'start_location.required' => __('msg.start_location_required'),
            'end_location.required' => __('msg.end_location_required'),
            'description.max' => __('msg.description_max'),
            'departure_time.required' => __('msg.departure_time_required'),
            'departure_time.date_format' => __('msg.departure_time_format'),
            'arrival_time.required' => __('msg.arrival_time_required'),
            'arrival_time.date_format' => __('msg.arrival_time_format'),
            'arrival_time.after' => __('msg.arrival_after_departure'),
            'distance_km.required' => __('msg.distance_required'),
            'distance_km.numeric' => __('msg.distance_numeric'),
            'distance_km.min' => __('msg.distance_min'),
            'distance_km.max' => __('msg.distance_max')
        ];
    }
}
