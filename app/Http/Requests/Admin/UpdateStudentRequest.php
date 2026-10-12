<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'nis' => ['required', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($student)],
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student?->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'classes' => ['array'],
            'classes.*' => [Rule::exists('classes', 'id')],
        ];
    }
}
