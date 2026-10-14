<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Course::class);
        $validated = $request->validate([
            'search' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'in:active,archived'],
            'sort' => ['sometimes', 'in:code,title,credit_unit,status,created_at'],
            'direction' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);
        $query = Course::query()->withCount('students');
        if ($search = $validated['search'] ?? null) {
            $query->where(fn ($query) => $query->where('code', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%"));
        }
        $query->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status));
        $direction = $validated['direction'] ?? 'asc';

        return CourseResource::collection($query->orderBy($validated['sort'] ?? 'code', $direction)->orderBy('id')->paginate($validated['per_page'] ?? 15)->withQueryString());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): CourseResource
    {
        Gate::authorize('create', Course::class);

        return new CourseResource(Course::create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): CourseResource
    {
        Gate::authorize('view', $course);

        return new CourseResource($course->loadCount('students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): CourseResource
    {
        Gate::authorize('update', $course);
        $course->update($request->validated());

        return new CourseResource($course->refresh()->loadCount('students'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): Response|JsonResponse
    {
        Gate::authorize('delete', $course);
        if ($course->students()->exists()) {
            $course->update(['status' => 'archived']);

            return response()->json(['message' => 'Course archived because it has enrolment history.']);
        }
        $course->delete();

        return response()->noContent();
    }
}
