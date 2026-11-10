<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Employee::where('is_active', true);

        if ($type = $request->query('type')) {
            $query->where('employment_type', $type);
        }

        $employees = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $employees,
        ]);
    }

    public function show(Employee $employee): JsonResponse
    {
        $employee->load('histories');

        return response()->json([
            'status' => 'success',
            'data' => $employee,
        ]);
    }
}
