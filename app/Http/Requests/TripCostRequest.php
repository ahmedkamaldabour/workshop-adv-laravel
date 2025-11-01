<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripCostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:local,intercity,international',
            'distance_km' => 'required|numeric|min:0|max:50000',
            'duration_hours' => 'required|numeric|min:0|max:168',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'The trip type is required.',
            'type.in' => 'The trip type must be one of: local, intercity, or international.',
            'distance_km.required' => 'The distance is required.',
            'distance_km.numeric' => 'The distance must be a valid number.',
            'distance_km.min' => 'The distance cannot be negative.',
            'distance_km.max' => 'The distance cannot exceed 50,000 km.',
            'duration_hours.required' => 'The duration is required.',
            'duration_hours.numeric' => 'The duration must be a valid number.',
            'duration_hours.min' => 'The duration cannot be negative.',
            'duration_hours.max' => 'The duration cannot exceed 168 hours (1 week).',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'trip type',
            'distance_km' => 'distance',
            'duration_hours' => 'duration',
        ];
    }
}

