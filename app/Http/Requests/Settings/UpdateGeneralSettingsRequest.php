<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
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
            'workspace_name' => [
                'required',
                'string',
                'max:20',
            ],
            'workspace_logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,webp',
                'max:2048',
            ],
            'workspace_favicon' => [
                'nullable',
                'image',
                'mimes:png,webp',
                'max:512',
            ],
            'timezone' => [
                'required',
                'string',
                'timezone',
            ],
            'date_format' => [
                'required',
                'string',
                'max:50',
            ],
            'time_format' => [
                'required',
                'string',
                'max:50',
            ],
            'currency' => [
                'required',
                'string',
                'max:10',
            ],
            'default_country' => [
                'required',
                'string',
                'max:10',
            ],
            'week_starts' => [
                'required',
                'in:monday,sunday',
            ],
            'business_start' => [
                'required',
                'date_format:H:i',
            ],
            'business_end' => [
                'required',
                'date_format:H:i',
                'after:business_start',
            ],
            'language' => [
                'required',
                'string',
                'max:10',
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'workspace_name.required' => 'Please enter a workspace name.',
            'workspace_name.max' => 'The workspace name may not be longer than 20 characters.',

            'workspace_logo.image' => 'The workspace logo must be a valid image.',
            'workspace_logo.mimes' => 'The workspace logo must be a JPEG, PNG, or WebP image.',
            'workspace_logo.max' => 'The workspace logo may not be larger than 2 MB.',

            'workspace_favicon.image' => 'The workspace favicon must be a valid image.',
            'workspace_favicon.mimes' => 'The workspace favicon must be a PNG or WebP image.',
            'workspace_favicon.max' => 'The workspace favicon may not be larger than 512 KB.',

            'timezone.required' => 'Please select a timezone.',
            'timezone.timezone' => 'Please select a valid timezone.',

            'date_format.required' => 'Please select a date format.',
            'time_format.required' => 'Please select a time format.',

            'currency.required' => 'Please select a currency.',
            'default_country.required' => 'Please select a default country.',

            'week_starts.required' => 'Please select the first day of the week.',
            'week_starts.in' => 'The week must start on Monday or Sunday.',

            'business_start.required' => 'Please enter the business start time.',
            'business_start.date_format' => 'The business start time must be in HH:MM format.',

            'business_end.required' => 'Please enter the business end time.',
            'business_end.date_format' => 'The business end time must be in HH:MM format.',
            'business_end.after' => 'The business end time must be after the business start time.',

            'language.required' => 'Please select a language.',
        ];
    }
}