<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGrade extends Model
{
    protected $fillable = [
        'grade_student_id',
        'term',
        'behavior',
        'applications',
        'notebook',
        'assignment',
        'exam',
    ];

    protected $casts = [
        'term' => 'integer',
        'behavior' => 'decimal:1',
        'applications' => 'decimal:1',
        'notebook' => 'decimal:1',
        'assignment' => 'decimal:2',
        'exam' => 'decimal:2',
    ];

    /**
     * Get the student this grade belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(GradeStudent::class, 'grade_student_id');
    }

    /**
     * Calculate the continuous assessment average.
     * CA = (behavior + applications + notebook) out of 15, scaled to 20.
     */
    public function getContinuousAssessmentAttribute(): float
    {
        $total = $this->behavior + $this->applications + $this->notebook;

        return round(($total / 15) * 20, 2);
    }

    /**
     * Calculate the final average.
     * Final = (CA + Assignment + Exam) / 3.
     */
    public function getFinalAverageAttribute(): float
    {
        return round(($this->continuous_assessment + $this->assignment + $this->exam) / 3, 2);
    }
}
