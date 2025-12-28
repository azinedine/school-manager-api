<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use App\Repositories\Contracts\TeacherRepositoryInterface;
use App\Models\User;

class TeacherController extends Controller
{
    protected $teacherRepository;

    /**
     * Constructor injection.
     */
    public function __construct(TeacherRepositoryInterface $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * Display a listing of teachers.
     */
    public function index()
    {
        $teachers = $this->teacherRepository->getAllTeachers();
        return TeacherResource::collection($teachers);
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->delete();

        return response()->json([
            'message' => 'Teacher account deleted successfully'
        ]);
    }
}
