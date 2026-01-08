<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'teacher_id',
        'title',
        'content',
        'lesson_date',
        'academic_year',
        'class_name',
        'subject_name',
        'status',
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    /**
     * Get the teacher who created this lesson
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the institution this lesson belongs to
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Scope to filter by teacher
     */
    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to filter by institution
     */
    public function scopeByInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by class name
     */
    public function scopeByClass($query, string $className)
    {
        return $query->where('class_name', $className);
    }

    /**
     * Scope to filter by subject name
     */
    public function scopeBySubject($query, string $subjectName)
    {
        return $query->where('subject_name', $subjectName);
    }

    /**
     * Scope to filter by academic year
     */
    public function scopeByAcademicYear($query, string $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('lesson_date', [$startDate, $endDate]);
    }

    /**
     * Scope to order by date (latest first)
     */
    public function scopeOrderByDate($query, string $direction = 'desc')
    {
        return $query->orderBy('lesson_date', $direction);
    }

    /**
     * Check if the lesson is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if the lesson is a draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
