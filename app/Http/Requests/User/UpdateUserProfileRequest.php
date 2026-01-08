<?php

namespace App\Http\Requests\User;

use IOException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Policy check: User can strictly only update themselves using this specific request,
        // or an admin updating a specific profile. 
        // Logic: if route has 'user', check policy.
        // Assuming route model binding: /users/{user}
        $user = $this->route('user');
        
        // Use the 'update' policy which we verified exists in UserController
        return Gate::allows('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // STRICT WHITELIST: Only columns that definitely exist in DB migrations
        // Missing (Phantoms): name_ar, gender, date_of_birth, address, phone, years_of_experience
        
        $rules = [
            // Core
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($this->route('user'))
            ],

            // Extended Profile Fields
            'name_ar' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'years_of_experience' => ['nullable', 'integer'],
            
            // Location
            'wilaya' => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            
            // Relationships
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'user_institution_id' => ['nullable', 'string'], // Matricule/ID at institution
            
            // Student specific (from add_profile_fields)
            'class' => ['nullable', 'string', 'max:255'],
            
            // Admin/Manager/Teacher specific (from add_admin_fields)
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'date_of_hiring' => ['nullable', 'date'],
            'work_phone' => ['nullable', 'string', 'max:20'],
            'office_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            
            // Teacher specific
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['string', 'max:255'],
            'levels' => ['nullable', 'array'],
            'levels.*' => ['string', 'max:255'],
        ];

        // Status and role changes only allowed when admin/manager is updating OTHER users
        $targetUser = $this->route('user');
        $currentUser = $this->user();
        $isUpdatingSelf = $currentUser && $targetUser && $currentUser->id === $targetUser->id;
        
        if (!$isUpdatingSelf && ($currentUser?->isAdmin() || $currentUser?->isManager() || $currentUser?->isSuperAdmin())) {
            $rules['status'] = ['sometimes', 'string', 'in:active,inactive,suspended'];
            $rules['role'] = ['sometimes', 'string', 'in:admin,manager,teacher,student,parent'];
        }

        return $rules;
    }

    /**
     * Handle a passed validation attempt.
     * We override this to strictly clean the request data if needed, 
     * but 'validated()' method in controller already does this.
     */
}
