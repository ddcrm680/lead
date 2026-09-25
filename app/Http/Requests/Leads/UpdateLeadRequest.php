<?php

namespace App\Http\Requests\Leads;

use App\Enums\LeadPriority;
use App\Models\Lead;
use App\Models\LeadFieldDefinition;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class UpdateLeadRequest extends FormRequest
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

            'contacts.*.id' => [
                'nullable',
                'integer',
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateContacts($validator);
            $this->validateDynamicAttributes($validator);
        });
    }

    /**
     * Validate Lead contacts.
     */
    private function validateContacts($validator): void
    {
        $contacts = $this->input('contacts', []);

        $primaryCount = collect($contacts)
            ->filter(fn ($contact) => !empty($contact['is_primary']))
            ->count();

        if ($primaryCount > 1) {
            $validator->errors()->add(
                'contacts',
                'Only one contact can be designated as the primary contact.'
            );
        }

        $phoneUtil = class_exists(PhoneNumberUtil::class)
            ? PhoneNumberUtil::getInstance()
            : null;

        $seenContacts = [];

        foreach ($contacts as $index => $contact) {
            $type = $contact['type'] ?? '';
            $value = trim($contact['value'] ?? '');

            // Duplicate detection within the incoming contacts payload
            if (!empty($type) && !empty($value)) {
                $cleanVal = strtolower(preg_replace('/\s+/', '', $value));
                $pair = "{$type}:{$cleanVal}";
                if (isset($seenContacts[$pair])) {
                    $validator->errors()->add(
                        "contacts.{$index}.value",
                        "This {$type} contact was entered multiple times in the form."
                    );
                }
                $seenContacts[$pair] = true;
            }

            if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $validator->errors()->add(
                    "contacts.{$index}.value",
                    'Please enter a valid email address.'
                );
            }

            if (
                in_array($type, ['phone', 'whatsapp'], true)
                && !empty($value)
            ) {
                if ($phoneUtil) {
                    try {
                        $parsed = $phoneUtil->parse(
                            $value,
                            PhoneNumberUtil::UNKNOWN_REGION
                        );

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
                    $digits = preg_replace('/\D/', '', $value);

                    if (strlen($digits) < 7 || strlen($digits) > 15) {
                        $validator->errors()->add(
                            "contacts.{$index}.value",
                            'Phone number must contain between 7 and 15 digits.'
                        );
                    }
                }
            }
        }
    }

    /**
     * Validate dynamic Lead attributes against active field definitions.
     */
    private function validateDynamicAttributes($validator): void
    {
        $attributes = $this->input('attributes', []);

        if (!is_array($attributes)) {
            return;
        }

        $definitions = LeadFieldDefinition::query()
            ->where('is_active', true)
            ->get([
                'key',
                'name',
                'type',
                'options',
                'validation_rules',
                'is_required',
            ]);

        $definitionsByKey = $definitions->keyBy('key');

        /*
         * Reject attributes that do not belong to an active Lead field definition.
         */
        foreach ($attributes as $key => $value) {
            if (!$definitionsByKey->has($key)) {
                $validator->errors()->add(
                    "attributes.{$key}",
                    'This field is not a valid active Lead field.'
                );
            }
        }

        /*
         * Validate every configured active field.
         */
        foreach ($definitions as $definition) {
            $key = $definition->key;
            $value = $attributes[$key] ?? null;

            if (
                $definition->is_required
                && ($value === null || $value === '')
            ) {
                $validator->errors()->add(
                    "attributes.{$key}",
                    "{$definition->name} is required."
                );

                continue;
            }

            /*
             * Optional empty fields do not need further validation.
             */
            if ($value === null || $value === '') {
                continue;
            }

            $this->validateDynamicFieldType(
                $validator,
                $definition,
                $value
            );
        }
    }

    /**
     * Validate the basic data type of a dynamic Lead field.
     */
    private function validateDynamicFieldType(
        $validator,
        LeadFieldDefinition $definition,
        mixed $value
    ): void {
        $attribute = "attributes.{$definition->key}";

        switch ($definition->type) {
            case 'text':
            case 'textarea':
                if (!is_string($value)) {
                    $validator->errors()->add(
                        $attribute,
                        "{$definition->name} must be text."
                    );
                }
                break;

            case 'number':
                if (
                    !is_int($value)
                    && !is_float($value)
                    && !(
                        is_string($value)
                        && is_numeric($value)
                    )
                ) {
                    $validator->errors()->add(
                        $attribute,
                        "{$definition->name} must be a number."
                    );
                }
                break;

            case 'select':
                $options = $definition->options ?? [];

                if (
                    is_array($options)
                    && !empty($options)
                    && !in_array($value, $options, true)
                ) {
                    $validator->errors()->add(
                        $attribute,
                        "Please select a valid {$definition->name}."
                    );
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add(
                        $attribute,
                        "{$definition->name} must be a valid email address."
                    );
                }
                break;

            case 'date':
                if (
                    !is_string($value)
                    || !strtotime($value)
                ) {
                    $validator->errors()->add(
                        $attribute,
                        "{$definition->name} must be a valid date."
                    );
                }
                break;
        }
    }
}
