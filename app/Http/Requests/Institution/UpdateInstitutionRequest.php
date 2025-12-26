<?php

namespace App\Http\Requests\Institution;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('institution'));
    }

    public function rules(): array
    {
        $institutionId = $this->route('institution')?->id;

        return [
            'wilaya_id' => ['sometimes', 'integer', 'exists:wilayas,id'],
            'municipality_id' => ['sometimes', 'integer', 'exists:municipalities,id'],
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('institutions')
                    ->where('municipality_id', $this->input('municipality_id', $this->route('institution')?->municipality_id))
                    ->whereNull('deleted_at')
                    ->ignore($institutionId),
            ],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'type' => ['sometimes', 'string', Rule::in(['primary', 'middle', 'secondary', 'university', 'vocational', 'other'])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'An institution with this name already exists in the selected municipality.',
        ];
    }
}
