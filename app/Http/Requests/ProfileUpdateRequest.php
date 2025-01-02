<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => [
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_img' => ['nullable', 'image', 'mimes:jpeg,png,gif', 'max:5120'],
            'class' => ['nullable', 'string', 'max:50'], // Thêm quy tắc xác thực cho 'class'
            'student_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($this->user()->id), // Đảm bảo mã sinh viên là duy nhất
            ],
            'phone' => ['nullable', 'string', 'max:15'], // Thêm quy tắc xác thực cho 'phone'
        ];
    }
}
