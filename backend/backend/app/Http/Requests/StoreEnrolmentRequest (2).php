<?php

namespace App\Http\Requests;

use App\Models\Enrolment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnrolmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Enrolment::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'session' => ['required', 'regex:/^\\d{4}\\/\\d{4}$/'],
            'semester' => ['required', 'in:first,second'],
            'status' => ['required', 'in:enrolled,completed,dropped'],
        ];
    }
}
