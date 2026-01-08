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
        $user = $this->route('user'); // The user resource being updated
        // Allow if user is updating themselves OR has permission to update others
        return $this->user()->id === $user->id || $this->user()->can('update', $user);
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
            
            // Extended Profile Fields
            'name_ar' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'work_phone' => ['nullable', 'string', 'max:20'],
            'office_location' => ['nullable', 'string', 'max:255'],
            'date_of_hiring' => ['nullable', 'date'],
            'years_of_experience' => ['nullable', 'integer'],
        ];
    }
}
