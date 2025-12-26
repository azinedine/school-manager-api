<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only super_admin can update roles
        return $this->user() && $this->user()->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('roles', 'name')->ignore($this->role->id),
            ],
            'display_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
        ];
    }
}
