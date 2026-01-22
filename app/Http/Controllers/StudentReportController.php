<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentReportResource;
use App\Models\GradeStudent;
use App\Models\StudentReport;
use Illuminate\Http\Request;

class StudentReportController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentReport::with(['student', 'teacher', 'institution']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('grade_class_id', $request->class_id);
            });
        }

        return StudentReportResource::collection($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:grade_students,id',
            'incident_description' => 'required|string',
            'sanctions' => 'nullable|array',
            'report_date' => 'required|date',
            'other_sanction' => 'nullable|string',
        ]);

        // Auto-resolve relationships
        $student = GradeStudent::findOrFail($validated['student_id']);
        // Assuming the authenticated user is the teacher
        $teacher = $request->user();

        // Find institution from student's class
        $institutionId = $student->grade_class->institution_id ?? 1; // Fallback or logic needed

        $reportNumber = $this->generateReportNumber($institutionId);

        $report = StudentReport::create([
            'institution_id' => $institutionId,
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'report_number' => $reportNumber,
            'academic_year' => '2024-2025', // Should be dynamic
            'report_date' => $validated['report_date'],
            'incident_description' => $validated['incident_description'],
            'sanctions' => $validated['sanctions'],
            'other_sanction' => $validated['other_sanction'] ?? null,
            'status' => 'finalized',
            'meta' => [
                'class_name' => $student->grade_class->name ?? 'Unknown',
                'student_name' => "$student->first_name $student->last_name",
            ],
        ]);

        return new StudentReportResource($report);
    }

    public function show(StudentReport $studentReport)
    {
        return new StudentReportResource($studentReport->load(['student', 'teacher']));
    }

    public function update(Request $request, StudentReport $studentReport)
    {
        // Add policy check later
        $validated = $request->validate([
            'incident_description' => 'string',
            'sanctions' => 'nullable|array',
            'other_sanction' => 'nullable|string',
        ]);

        $studentReport->update($validated);

        return new StudentReportResource($studentReport);
    }

    public function destroy(StudentReport $studentReport)
    {
        $studentReport->delete();

        return response()->noContent();
    }

    private function generateReportNumber($institutionId)
    {
        // Simple generation logic, can be improved
        $count = StudentReport::where('institution_id', $institutionId)->count();

        return sprintf('R-%04d', $count + 1);
    }
}
