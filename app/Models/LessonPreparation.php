<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class LessonPreparation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'title',
        'subject',
        'class',
        'date',
        'duration_minutes',
        'learning_objectives',
        'description',
        'key_topics',
        'teaching_methods',
        'resources_needed',
        'assessment_methods',
        'assessment_criteria',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'learning_objectives' => 'array',
        'key_topics' => 'array',
        'teaching_methods' => 'array',
        'resources_needed' => 'array',
        'assessment_methods' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the teacher who created this lesson preparation
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by class
     */
    public function scopeByClass($query, string $class)
    {
        return $query->where('class', $class);
    }

    /**
     * Scope to filter by subject
     */
    public function scopeBySubject($query, string $subject)
    {
        return $query->where('subject', $subject);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by teacher
     */
    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to get ordered by date (latest first)
     */
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('date', 'desc');
    }
}
