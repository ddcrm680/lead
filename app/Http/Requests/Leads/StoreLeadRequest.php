<?php

namespace App\Http\Requests\Leads;

use App\Enums\LeadPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class StoreLeadRequest extends FormRequest
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
            'display_name' => [
                'required',
                'string',
                'max:255',
            ],

            'source_id' => [
                'nullable',
                'integer',
                'exists:lead_sources,id',
            ],

            'status_id' => [
                'required',
                'integer',
                'exists:lead_statuses,id',
            ],

            'pipeline_stage_id' => [
                'nullable',
                'integer',
                'exists:pipeline_stages,id',
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'priority' => [
                'nullable',
                Rule::enum(LeadPriority::class),
            ],

            'city' => [
                'nullable',
                'string',
                'max:150',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'attributes' => [
                'nullable',
                'array',
            ],

            'contacts' => [
                'nullable',
                'array',
                'max:20',
            ],

            'contacts.*.type' => [
                'required',
                'string',
                Rule::in([
                    'phone',
                    'email',
                    'whatsapp',
                ]),
            ],

            'contacts.*.value' => [
                'required',
                'string',
                'max:255',
            ],

            'contacts.*.is_primary' => [
                'sometimes',
                'boolean',
            ],

            'tags' => [
                'nullable',
                'array',
            ],

            'tags.*' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * Configure the validator instance for custom checks.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $contacts = $this->input('contacts', []);

            // 1. Enforce single primary contact rule
            $primaryCount = collect($contacts)
                ->filter(fn ($contact) => !empty($contact['is_primary']))
                ->count();

            if ($primaryCount > 1) {
                $validator->errors()->add('contacts', 'Only one contact can be designated as the primary contact.');
            }

            // 2. Validate format: Email and International E.164 Phone/WhatsApp
            $phoneUtil = class_exists(PhoneNumberUtil::class) ? PhoneNumberUtil::getInstance() : null;

            foreach ($contacts as $index => $contact) {
                $type = $contact['type'] ?? '';
                $val = trim($contact['value'] ?? '');

                if ($type === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add("contacts.{$index}.value", 'Please enter a valid email address.');
                }

                if (in_array($type, ['phone', 'whatsapp'], true) && !empty($val)) {
                    if ($phoneUtil) {
                        try {
                            $parsed = $phoneUtil->parse($val, PhoneNumberUtil::UNKNOWN_REGION);
                            if (!$phoneUtil->isValidNumber($parsed)) {
                                $validator->errors()->add(
                                    "contacts.{$index}.value",
                                    'Please enter a valid international number with country code (e.g. +14155552671, +442079460912).'
                                );
                            }
                        } catch (NumberParseException $e) {
                            $validator->errors()->add(
                                "contacts.{$index}.value",
                                'Invalid phone format. Please include your country code starting with + (e.g. +1... or +44...).'
                            );
                        }
                    } else {
                        // Fallback validation if libphonenumber is not installed
                        $digits = preg_replace('/\D/', '', $val);
                        if (strlen($digits) < 7 || strlen($digits) > 15) {
                            $validator->errors()->add("contacts.{$index}.value", 'Phone number must contain between 7 and 15 digits.');
                        }
                    }
                }
            }
        });
    }
}