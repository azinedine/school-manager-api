<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeClass extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'subject',
        'grade_level',
        'academic_year',
    ];

    /**
     * Get the teacher that owns this class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the students in this class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(GradeStudent::class)->orderBy('sort_order');
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeForYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to filter by teacher.
     */
    public function scopeForTeacher($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
