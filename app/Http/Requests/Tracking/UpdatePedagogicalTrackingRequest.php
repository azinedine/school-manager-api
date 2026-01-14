<?php

namespace App\Http\Requests\Tracking;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePedagogicalTrackingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller
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
            'term' => 'required|integer|in:1,2,3',
            'oral_interrogation' => 'sometimes|boolean',
            'notebook_checked' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'term' => 'term number',
            'oral_interrogation' => 'oral interrogation status',
            'notebook_checked' => 'notebook checked status',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'term.in' => 'The term must be 1, 2, or 3.',
            'oral_interrogation.boolean' => 'Oral interrogation must be true or false.',
            'notebook_checked.boolean' => 'Notebook checked must be true or false.',
        ];
    }
}
