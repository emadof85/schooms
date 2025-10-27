<?php

namespace App\Http\Requests\Bus;

use Illuminate\Foundation\Http\FormRequest;

class StudentBusAssignmentRequest extends FormRequest
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
            'student_id' => 'required|exists:student_records,id',
            'bus_id' => 'required|exists:buses,id',
            'bus_stop_id' => 'required|exists:bus_stops,id',
            'assignment_date' => 'required|date',
            'end_date' => 'nullable|date|after:assignment_date',
            'fee_amount' => 'required|numeric|min:0|max:999999.99',
            'notes' => 'nullable|string|max:500',
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
            'student_id.required' => __('msg.student_required'),
            'student_id.exists' => __('msg.student_exists'),
            'bus_id.required' => __('msg.bus_required'),
            'bus_id.exists' => __('msg.bus_exists'),
            'bus_stop_id.required' => __('msg.bus_stop_required'),
            'bus_stop_id.exists' => __('msg.bus_stop_exists'),
            'assignment_date.required' => __('msg.assignment_date_required'),
            'assignment_date.date' => __('msg.assignment_date_invalid'),
            'end_date.date' => __('msg.end_date_invalid'),
            'end_date.after' => __('msg.end_date_after_assignment'),
            'fee_amount.required' => __('msg.fee_amount_required'),
            'fee_amount.numeric' => __('msg.fee_amount_numeric'),
            'fee_amount.min' => __('msg.fee_amount_min'),
            'fee_amount.max' => __('msg.fee_amount_max'),
            'notes.max' => __('msg.notes_max')
        ];
    }
}
