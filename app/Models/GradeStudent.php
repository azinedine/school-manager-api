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

    /**
     * Get all pedagogical tracking records for this student.
     */
    public function pedagogicalTracking(): HasMany
    {
        return $this->hasMany(StudentPedagogicalTracking::class);
    }

    /**
     * Get tracking for a specific term.
     */
    public function trackingForTerm(int $term): HasOne
    {
        return $this->hasOne(StudentPedagogicalTracking::class)->where('term', $term);
    }

    /**
     * Get or create tracking for a specific term.
     */
    public function getOrCreateTermTracking(int $term): StudentPedagogicalTracking
    {
        return $this->pedagogicalTracking()->firstOrCreate(
            ['term' => $term],
            [
                'oral_interrogation' => false,
                'notebook_checked' => false,
            ]
        );
    }

    /**
     * Get all reports for this student.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(StudentReport::class, 'student_id');
    }

    /**
     * Get all weekly reviews for this student.
     */
    public function weeklyReviews(): HasMany
    {
        return $this->hasMany(StudentWeeklyReview::class, 'grade_student_id');
    }

    /**
     * Get the latest weekly review for this student.
     */
    public function latestWeeklyReview(): HasOne
    {
        return $this->hasOne(StudentWeeklyReview::class, 'grade_student_id')
            ->orderByDesc('year')
            ->orderByDesc('week_number');
    }
}
