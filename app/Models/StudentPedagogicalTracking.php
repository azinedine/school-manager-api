<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPedagogicalTracking extends Model
{
    protected $table = 'student_pedagogical_tracking';

    protected $fillable = [
        'grade_student_id',
        'term',
        'oral_interrogation',
        'notebook_checked',
        'last_interrogation_at',
        'last_notebook_check_at',
    ];

    protected $casts = [
        'term' => 'integer',
        'oral_interrogation' => 'boolean',
        'notebook_checked' => 'boolean',
        'last_interrogation_at' => 'datetime',
        'last_notebook_check_at' => 'datetime',
    ];

    /**
     * Get the student this tracking record belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(GradeStudent::class, 'grade_student_id');
    }

    /**
     * Mark oral interrogation as done with timestamp.
     */
    public function markInterrogated(): void
    {
        $this->update([
            'oral_interrogation' => true,
            'last_interrogation_at' => now(),
        ]);
    }

    /**
     * Mark notebook as checked with timestamp.
     */
    public function markNotebookChecked(): void
    {
        $this->update([
            'notebook_checked' => true,
            'last_notebook_check_at' => now(),
        ]);
    }

    /**
     * Toggle oral interrogation status.
     */
    public function toggleInterrogation(): void
    {
        $newStatus = !$this->oral_interrogation;
        $this->update([
            'oral_interrogation' => $newStatus,
            'last_interrogation_at' => $newStatus ? now() : $this->last_interrogation_at,
        ]);
    }

    /**
     * Toggle notebook checked status.
     */
    public function toggleNotebookChecked(): void
    {
        $newStatus = !$this->notebook_checked;
        $this->update([
            'notebook_checked' => $newStatus,
            'last_notebook_check_at' => $newStatus ? now() : $this->last_notebook_check_at,
        ]);
    }
}
