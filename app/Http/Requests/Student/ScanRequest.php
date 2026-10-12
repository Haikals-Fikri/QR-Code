<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class ScanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isSiswa() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'size:64'],
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
            'token.required' => 'QR tidak terbaca. Coba pindai ulang.',
            'token.size' => 'QR tidak valid.',
        ];
    }
}
