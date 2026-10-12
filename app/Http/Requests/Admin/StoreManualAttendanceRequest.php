<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualAttendanceRequest extends FormRequest
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
            'student_id' => ['required', Rule::exists('students', 'id')],
            'session_id' => ['required', Rule::exists('schedule_sessions', 'id')],
            'status' => ['required', Rule::in(['hadir', 'telat', 'izin', 'sakit'])],
        ];
    }

    /**
     * Pesan validasi kustom.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Pilih siswa.',
            'session_id.required' => 'Pilih sesi.',
            'status.required' => 'Pilih status kehadiran.',
        ];
    }
}
