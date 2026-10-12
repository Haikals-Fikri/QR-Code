<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassRoomRequest extends FormRequest
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
        return [
            'nama' => ['required', 'string', 'max:255'],
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classes', 'kode')->ignore($this->route('classRoom')),
            ],
            'guru_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'admin')],
        ];
    }
}
