<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'password' => ['sometimes', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['sometimes', 'string', 'in:admin,manager,teacher,student,parent'],
            'status' => ['sometimes', 'string', 'in:active,inactive,suspended'],
            'wilaya' => ['nullable', 'string'],
            'municipality' => ['nullable', 'string'],
            'institution_id' => ['nullable', 'exists:institutions,id'],
        ];
    }
}
