<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableRequest extends FormRequest
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
            'entries' => ['required', 'array'],
            'entries.*.day' => ['required', 'string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'entries.*.time_slot' => ['required', 'string'],
            'entries.*.class' => ['required', 'string'],
            'entries.*.mode' => ['required', 'string', 'in:fullClass,groups'],
            'entries.*.group' => ['nullable', 'string', 'in:first,second'],
        ];
    }
}
