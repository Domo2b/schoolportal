<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            $student = $request->user()->student;

            return response()->json([
                'courses' => $student?->courses()->distinct()->count() ?? 0,
                'enrolments' => $student?->enrolments()->where('status', 'enrolled')->count() ?? 0,
                'completed_courses' => $student?->enrolments()->where('status', 'completed')->count() ?? 0,
            ]);
        }

        return response()->json([
            'students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'courses' => Course::where('status', 'active')->count(),
            'enrolments' => Enrolment::count(),
        ]);
    }
}
