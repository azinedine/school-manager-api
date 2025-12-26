<?php

namespace App\Http\Requests\Institution;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Institution::class);
    }

    public function rules(): array
    {
        return [
            'wilaya_id' => ['required', 'integer', 'exists:wilayas,id'],
            'municipality_id' => ['required', 'integer', 'exists:municipalities,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutions')
                    ->where('municipality_id', $this->input('municipality_id'))
                    ->whereNull('deleted_at'),
            ],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'type' => ['required', 'string', Rule::in(['primary', 'middle', 'secondary', 'university', 'vocational', 'other'])],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'An institution with this name already exists in the selected municipality.',
            'municipality_id.exists' => 'The selected municipality is invalid.',
            'wilaya_id.exists' => 'The selected wilaya is invalid.',
        ];
    }
}
