<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentWeeklyReview extends Model
{
    /**
     * Observation type constants.
     */
    public const OBSERVATION_OK = 'OK';
    public const OBSERVATION_NO_NOTEBOOK = 'NO_NOTEBOOK';
    public const OBSERVATION_LESSON_NOT_WRITTEN = 'LESSON_NOT_WRITTEN';
    public const OBSERVATION_INCOMPLETE = 'INCOMPLETE';
    public const OBSERVATION_HOMEWORK_MISSING = 'HOMEWORK_MISSING';
    public const OBSERVATION_COMMUNICATION_NOTE = 'COMMUNICATION_NOTE';
    public const OBSERVATION_MULTIPLE_ISSUES = 'MULTIPLE_ISSUES';

    /**
     * Valid observation types.
     */
    public const OBSERVATION_TYPES = [
        self::OBSERVATION_OK,
        self::OBSERVATION_NO_NOTEBOOK,
        self::OBSERVATION_LESSON_NOT_WRITTEN,
        self::OBSERVATION_INCOMPLETE,
        self::OBSERVATION_HOMEWORK_MISSING,
        self::OBSERVATION_COMMUNICATION_NOTE,
        self::OBSERVATION_MULTIPLE_ISSUES,
    ];

    protected $fillable = [
        'grade_student_id',
        'grade_class_id',
        'teacher_id',
        'year',
        'week_number',
        'week_start_date',
        'notebook_checked',
        'lesson_written',
        'homework_done',
        'score',
        'observation_type',
        'observation_notes',
        'alert_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'week_number' => 'integer',
        'week_start_date' => 'date',
        'notebook_checked' => 'boolean',
        'lesson_written' => 'boolean',
        'homework_done' => 'boolean',
        'score' => 'decimal:2',
        'alert_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the student this review belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(GradeStudent::class, 'grade_student_id');
    }

    /**
     * Get the class this review belongs to.
     */
    public function gradeClass(): BelongsTo
    {
        return $this->belongsTo(GradeClass::class, 'grade_class_id');
    }

    /**
     * Get the teacher who created this review.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Check if this review has an issue (observation != OK).
     */
    public function hasIssue(): bool
    {
        return $this->observation_type !== self::OBSERVATION_OK;
    }

    /**
     * Check if this review has a pending alert.
     */
    public function hasPendingAlert(): bool
    {
        return $this->hasIssue() && ! $this->alert_resolved;
    }

    /**
     * Mark the alert as resolved.
     */
    public function markResolved(): void
    {
        $this->update([
            'alert_resolved' => true,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Scope: Filter by class.
     */
    public function scopeForClass($query, string $classId)
    {
        return $query->where('grade_class_id', $classId);
    }

    /**
     * Scope: Filter by week.
     */
    public function scopeForWeek($query, int $year, int $week)
    {
        return $query->where('year', $year)->where('week_number', $week);
    }

    /**
     * Scope: Filter by student.
     */
    public function scopeForStudent($query, string $studentId)
    {
        return $query->where('grade_student_id', $studentId);
    }

    /**
     * Scope: Get reviews with issues (not OK).
     */
    public function scopeWithIssues($query)
    {
        return $query->where('observation_type', '!=', self::OBSERVATION_OK);
    }

    /**
     * Scope: Get unresolved alerts.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('alert_resolved', false);
    }

    /**
     * Scope: Get pending alerts (has issue and not resolved).
     */
    public function scopePendingAlerts($query)
    {
        return $query->withIssues()->unresolved();
    }

    /**
     * Get current ISO week info.
     */
    public static function getCurrentWeek(): array
    {
        $now = now();

        return [
            'year' => (int) $now->format('o'),
            'week' => (int) $now->format('W'),
            'week_start' => $now->startOfWeek()->format('Y-m-d'),
        ];
    }

    /**
     * Get last week ISO info.
     */
    public static function getLastWeek(): array
    {
        $current = self::getCurrentWeek();

        if ($current['week'] === 1) {
            // Handle year rollover
            $lastYear = $current['year'] - 1;
            $lastWeekDate = now()->subWeek();

            return [
                'year' => (int) $lastWeekDate->format('o'),
                'week' => (int) $lastWeekDate->format('W'),
                'week_start' => $lastWeekDate->startOfWeek()->format('Y-m-d'),
            ];
        }

        return [
            'year' => $current['year'],
            'week' => $current['week'] - 1,
            'week_start' => now()->subWeek()->startOfWeek()->format('Y-m-d'),
        ];
    }

    /**
     * Calculate week start date from year and week number.
     */
    public static function calculateWeekStartDate(int $year, int $week): string
    {
        $date = new \DateTime();
        $date->setISODate($year, $week, 1); // 1 = Monday

        return $date->format('Y-m-d');
    }
}
