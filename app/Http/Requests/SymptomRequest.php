<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SymptomRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'severity' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string'],
            'recorded_at' => ['required', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'severity.min' => 'Severity must be between 1 and 10.',
            'severity.max' => 'Severity must be between 1 and 10.',
        ];
    }
}

