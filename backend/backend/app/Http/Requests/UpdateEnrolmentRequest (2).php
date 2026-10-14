<?php

namespace App\Http\Requests;

use App\Models\Enrolment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrolmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $enrolment = $this->route('enrolment');

        return $enrolment instanceof Enrolment && ($this->user()?->can('update', $enrolment) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:enrolled,completed,dropped'],
        ];
    }
}
