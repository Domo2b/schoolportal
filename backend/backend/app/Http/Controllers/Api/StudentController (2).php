<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Student::class);
        $validated = $request->validate([
            'search' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'in:active,inactive'],
            'department' => ['sometimes', 'string', 'max:100'],
            'sort' => ['sometimes', 'in:name,matric_no,department,level,status,created_at'],
            'direction' => ['sometimes', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);
        $query = Student::query()->with('user');
        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }
        if ($search = $validated['search'] ?? null) {
            $query->where(function ($query) use ($search): void {
                $query->where('matric_no', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }
        $query->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status));
        $query->when($validated['department'] ?? null, fn ($query, $department) => $query->where('department', $department));
        $sort = $validated['sort'] ?? 'created_at';
        $direction = $validated['direction'] ?? 'desc';
        if ($sort === 'name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'students.user_id'), $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        return StudentResource::collection($query->orderBy('id', $direction)->paginate($validated['per_page'] ?? 15)->withQueryString());
    }

    public function store(StoreStudentRequest $request): StudentResource
    {
        Gate::authorize('create', Student::class);
        $data = $request->validated();
        $student = DB::transaction(function () use ($data): Student {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'student',
            ]);

            return $user->student()->create(collect($data)->only(['matric_no', 'department', 'level', 'status'])->all());
        });

        return new StudentResource($student->load('user'));
    }

    public function show(Student $student): StudentResource
    {
        Gate::authorize('view', $student);

        return new StudentResource($student->load('user'));
    }

    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        Gate::authorize('update', $student);
        $data = $request->validated();
        DB::transaction(function () use ($data, $request, $student): void {
            if ($request->boolean('remove_avatar') && $student->user->avatar) {
                Storage::disk('public')->delete($student->user->avatar);
                $data['avatar'] = null;
            }
            if ($request->hasFile('avatar')) {
                if ($student->user->avatar) {
                    Storage::disk('public')->delete($student->user->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }
            $student->user->update(collect($data)->only(['name', 'email', 'password'])->filter(fn ($value) => $value !== null && $value !== '')->all());
            if (array_key_exists('avatar', $data)) {
                $student->user->update(['avatar' => $data['avatar']]);
            }
            $student->update(collect($data)->only(['matric_no', 'department', 'level', 'status'])->all());
        });

        return new StudentResource($student->refresh()->load('user'));
    }

    public function destroy(Student $student): Response
    {
        Gate::authorize('delete', $student);
        $student->user()->firstOrFail()->delete();

        return response()->noContent();
    }
}
