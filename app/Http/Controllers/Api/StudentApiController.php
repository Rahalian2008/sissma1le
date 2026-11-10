<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($class = $request->query('class_level')) {
            $query->where('current_class_level', $class);
        }

        $students = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $students,
        ]);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['family', 'mutations', 'achievements', 'violations', 'scholarships', 'alumnus']);

        return response()->json([
            'status' => 'success',
            'data' => $student,
        ]);
    }
}
