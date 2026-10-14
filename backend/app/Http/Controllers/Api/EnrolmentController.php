<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrolmentRequest;
use App\Http\Requests\UpdateEnrolmentRequest;
use App\Http\Resources\EnrolmentResource;
use App\Models\Enrolment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class EnrolmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Enrolment::class);
        $validated = $request->validate([
            'student_id' => ['sometimes', 'integer', 'exists:students,id'],
            'course_id' => ['sometimes', 'integer', 'exists:courses,id'],
            'session' => ['sometimes', 'regex:/^\\d{4}\\/\\d{4}$/'],
            'semester' => ['sometimes', 'in:first,second'],
            'status' => ['sometimes', 'in:enrolled,completed,dropped'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);
        $query = Enrolment::query()->with(['student.user', 'course']);
        if (! $request->user()->isAdmin()) {
            $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('user_id', $request->user()->id));
        }
        foreach (['student_id', 'course_id', 'session', 'semester', 'status'] as $filter) {
            $query->when($validated[$filter] ?? null, fn ($query, $value) => $query->where($filter, $value));
        }

        return EnrolmentResource::collection($query->latest()->orderByDesc('id')->paginate($validated['per_page'] ?? 15)->withQueryString());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnrolmentRequest $request): EnrolmentResource
    {
        Gate::authorize('create', Enrolment::class);
        $data = $request->validated();
        $exists = Enrolment::query()->where(collect($data)->only(['student_id', 'course_id', 'session', 'semester'])->all())->exists();
        if ($exists) {
            throw ValidationException::withMessages(['course_id' => ['This student is already enrolled for the selected period.']]);
        }

        return new EnrolmentResource(Enrolment::create($data)->load(['student.user', 'course']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrolment $enrolment): EnrolmentResource
    {
        Gate::authorize('view', $enrolment);

        return new EnrolmentResource($enrolment->load(['student.user', 'course']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrolmentRequest $request, Enrolment $enrolment): EnrolmentResource
    {
        Gate::authorize('update', $enrolment);
        $enrolment->update($request->validated());

        return new EnrolmentResource($enrolment->refresh()->load(['student.user', 'course']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrolment $enrolment): Response
    {
        Gate::authorize('delete', $enrolment);
        $enrolment->delete();

        return response()->noContent();
    }
}
