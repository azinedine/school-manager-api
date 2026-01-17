<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'wilaya',
        'municipality',
        'institution_id',
        'user_institution_id',
        'class',
        'linked_student_id',
        'subjects',
        'levels',
        // Admin Profile
        'department',
        'position',
        'date_of_hiring',
        'work_phone',
        'office_location',
        'notes',

        // Extended Profile
        'name_ar',
        'gender',
        'date_of_birth',
        'address',
        'phone',

        // Teacher Specific
        'teacher_id',
        'years_of_experience',
        'employment_status',
        'weekly_teaching_load',
        'assigned_classes',
        'groups',

        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'subjects' => 'array',
            'levels' => 'array',
            'assigned_classes' => 'array',
            'groups' => 'array',
        ];
    }

    // Role Constants
    const ROLE_SUPER_ADMIN = 'super_admin';

    const ROLE_ADMIN = 'admin';

    const ROLE_MANAGER = 'manager';

    const ROLE_TEACHER = 'teacher';

    const ROLE_STUDENT = 'student';

    const ROLE_PARENT = 'parent';

    // Helper Methods
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class, 'wilaya', 'id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality', 'id');
    }

    /**
     * Scope a query to only include users belonging to a specific institution.
     */
    /**
     * Get the user's timetable entries
     */
    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'teacher_id');
    }

    public function lessonPreparations()
    {
        return $this->hasMany(LessonPreparation::class, 'teacher_id');
    }

    public function scopeForInstitution($query, string|int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
