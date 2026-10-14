<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $student = $this->route('student');

        return $student instanceof Student && ($this->user()?->can('update', $student) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('student')->user_id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
            'matric_no' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('students')->ignore($this->route('student'))],
            'department' => ['sometimes', 'required', 'string', 'max:100'],
            'level' => ['sometimes', 'required', 'integer', 'between:100,900'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }
}
