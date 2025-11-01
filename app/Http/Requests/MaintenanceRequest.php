<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceRequest extends FormRequest
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
            'vehicle_id' => 'required|integer',
            'issue_type' => 'required|string|in:engine,tires,electrical',
            'description' => 'nullable|string|max:1000',
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
            'vehicle_id.required' => 'The vehicle ID is required.',
            'vehicle_id.integer' => 'The vehicle ID must be a valid number.',
            'issue_type.required' => 'The issue type is required.',
            'issue_type.in' => 'The issue type must be one of: engine, tires, or electrical.',
            'description.max' => 'The description may not exceed 1000 characters.',
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
            'vehicle_id' => 'vehicle ID',
            'issue_type' => 'issue type',
            'description' => 'description',
        ];
    }
}

