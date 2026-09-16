<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'phone_country_code' => [
                'nullable',
                'string',
                'max:5',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the user\'s name.',
            'name.max' => 'The name may not be longer than 150 characters.',

            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'The email address may not be longer than 255 characters.',
            'email.unique' => 'This email address is already in use.',

            'phone_country_code.max' => 'The country code may not be longer than 5 characters.',

            'phone.max' => 'The phone number may not be longer than 30 characters.',

            'address.max' => 'The address may not be longer than 500 characters.',

            'role_id.required' => 'Please select a role.',
            'role_id.integer' => 'The selected role is invalid.',
            'role_id.exists' => 'The selected role does not exist.',

            'is_active.required' => 'Please select the user status.',
            'is_active.boolean' => 'The selected user status is invalid.',

            'password.required' => 'Please enter a password.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',

            'avatar.image' => 'The avatar must be a valid image.',
            'avatar.mimes' => 'The avatar must be a JPEG, PNG, or WebP image.',
            'avatar.max' => 'The avatar may not be larger than 2 MB.',
        ];
    }
}