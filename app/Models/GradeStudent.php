<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GradeStudent extends Model
{
    use HasUuids;

    protected $fillable = [
        'grade_class_id',
        'student_number',
        'last_name',
        'first_name',
        'date_of_birth',
        'special_case',
        'sort_order',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'sort_order' => 'integer',
    ];

    /**
     * Get the class this student belongs to.
     */
    public function gradeClass(): BelongsTo
    {
        return $this->belongsTo(GradeClass::class);
    }

    /**
     * Get all term grades for this student.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Get grades for a specific term.
     */
    public function gradesForTerm(int $term): HasOne
    {
        return $this->hasOne(StudentGrade::class)->where('term', $term);
    }

    /**
     * Get or create grades for a specific term.
     */
    public function getOrCreateTermGrades(int $term): StudentGrade
    {
        return $this->grades()->firstOrCreate(
            ['term' => $term],
            [
                'behavior' => 5,
                'applications' => 5,
                'notebook' => 5,
                'assignment' => 0,
                'exam' => 0,
            ]
        );
    }
}
