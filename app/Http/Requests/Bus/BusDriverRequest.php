<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;

class BusDriverRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'bus_id' => 'required|exists:buses,id',
            'assignment_date' => 'required|date',
            'end_date' => 'nullable|date|after:assignment_date',
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
            'employee_id.required' => __('msg.employee_required'),
            'employee_id.exists' => __('msg.employee_exists'),
            'bus_id.required' => __('msg.bus_required'),
            'bus_id.exists' => __('msg.bus_exists'),
            'assignment_date.required' => __('msg.assignment_date_required'),
            'assignment_date.date' => __('msg.assignment_date_invalid'),
            'end_date.date' => __('msg.end_date_invalid'),
            'end_date.after' => __('msg.end_date_after_assignment')
        ];
    }
}
