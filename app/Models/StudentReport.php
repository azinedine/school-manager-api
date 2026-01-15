<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    protected $fillable = [
        'institution_id',
        'teacher_id',
        'student_id',
        'report_number',
        'academic_year',
        'report_date',
        'incident_description',
        'sanctions',
        'other_sanction',
        'status',
        'meta',
    ];

    protected $casts = [
        'report_date' => 'date',
        'sanctions' => 'array',
        'meta' => 'array',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(GradeStudent::class, 'student_id');
    }
}
