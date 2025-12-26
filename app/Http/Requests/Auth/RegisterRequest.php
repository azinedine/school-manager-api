<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,admin,teacher,student,parent'],
            'wilaya' => ['nullable', 'string'],
            'municipality' => ['nullable', 'string'],
            'institution' => ['nullable', 'string'], // Receives name or ID
            'subjects' => ['nullable', 'array'],
            'levels' => ['nullable', 'array'],
            'class' => ['nullable', 'string'],
            'linked_student_id' => ['nullable', 'string'],
        ];
    }
}
