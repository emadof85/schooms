<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;

class BusStopRequest extends FormRequest
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
            'stop_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'order' => 'required|integer|min:1',
            'bus_route_id' => 'required|exists:bus_routes,id',
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
            'stop_name.required' => __('msg.stop_name_required'),
            'address.required' => __('msg.address_required'),
            'latitude.numeric' => __('msg.latitude_numeric'),
            'latitude.between' => __('msg.latitude_range'),
            'longitude.numeric' => __('msg.longitude_numeric'),
            'longitude.between' => __('msg.longitude_range'),
            'order.required' => __('msg.order_required'),
            'order.integer' => __('msg.order_integer'),
            'order.min' => __('msg.order_min'),
            'bus_route_id.required' => __('msg.bus_route_required'),
            'bus_route_id.exists' => __('msg.bus_route_exists')
        ];
    }
}
