<?php

namespace App\Services;

use App\Enums\LeadPriority;
use App\Models\LeadFieldDefinition;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Illuminate\Database\Eloquent\Collection;

class LeadPayloadValidator
{
    private ?Collection $fieldDefinitions = null;

    /**
     * Validation rules for a normalized Lead creation payload.
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
                'required',
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
     * Attach Lead-specific validation that cannot be expressed
     * using basic Laravel validation rules alone.
     */
    public function after(
        Validator $validator,
        array $data,
        array $allowedAttributeKeys = [],
    ): void {
        $validator->after(function (
            Validator $validator
        ) use (
            $data,
            $allowedAttributeKeys,
        ): void {
            $this->validateContacts(
                $validator,
                $data['contacts'] ?? [],
            );

            $this->validateDynamicAttributes(
                $validator,
                $data['attributes'] ?? [],
                $allowedAttributeKeys,
            );
        });
    }

    /**
     * Validate Lead contacts.
     */
    private function validateContacts(
        Validator $validator,
        mixed $contacts,
    ): void {
        if (! is_array($contacts)) {
            return;
        }

        $primaryCount = collect($contacts)
            ->filter(
                fn ($contact) =>
                    ! empty($contact['is_primary'])
            )
            ->count();

        if ($primaryCount > 1) {
            $validator->errors()->add(
                'contacts',
                'Only one contact can be designated as the primary contact.'
            );
        }

        $phoneUtil = class_exists(
            PhoneNumberUtil::class
        )
            ? PhoneNumberUtil::getInstance()
            : null;

        foreach ($contacts as $index => $contact) {
            if (! is_array($contact)) {
                continue;
            }

            $type = $contact['type'] ?? '';

            $value = trim(
                (string) ($contact['value'] ?? '')
            );

            if (
                $type === 'email'
                && ! filter_var(
                    $value,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $validator->errors()->add(
                    "contacts.{$index}.value",
                    'Please enter a valid email address.'
                );
            }

            if (
                ! in_array(
                    $type,
                    [
                        'phone',
                        'whatsapp',
                    ],
                    true
                )
                || $value === ''
            ) {
                continue;
            }

            if (! $phoneUtil) {
                $digits = preg_replace(
                    '/\D/',
                    '',
                    $value
                );

                if (
                    strlen($digits) < 7
                    || strlen($digits) > 15
                ) {
                    $validator->errors()->add(
                        "contacts.{$index}.value",
                        'Phone number must contain between 7 and 15 digits.'
                    );
                }

                continue;
            }

            try {
                $parsed = $phoneUtil->parse(
                    $value,
                    PhoneNumberUtil::UNKNOWN_REGION
                );

                if (
                    ! $phoneUtil->isValidNumber(
                        $parsed
                    )
                ) {
                    $validator->errors()->add(
                        "contacts.{$index}.value",
                        'Please enter a valid international number with country code (e.g. +14155552671, +442079460912).'
                    );
                }
            } catch (NumberParseException) {
                $validator->errors()->add(
                    "contacts.{$index}.value",
                    'Invalid phone format. Please include your country code starting with + (e.g. +1... or +44...).'
                );
            }
        }
    }

    /**
     * Validate dynamic Lead attributes against active definitions.
     */
    private function validateDynamicAttributes(
        Validator $validator,
        mixed $attributes,
        array $allowedAttributeKeys = [],

    ): void {
        if (! is_array($attributes)) {
            return;
        }

        $definitions = $this->fieldDefinitions ??=
            LeadFieldDefinition::query()
                ->where('is_active', true)
                ->get([
                    'key',
                    'name',
                    'type',
                    'options',
                    'validation_rules',
                    'is_required',
                ]);

        $definitionsByKey =
            $definitions->keyBy('key');

        $allowedAttributeLookup = collect(
            $allowedAttributeKeys
        )
            ->map(
                static fn ($key): string =>
                    trim((string) $key)
            )
            ->filter()
            ->flip();

        foreach ($attributes as $key => $value) {
            if (
                ! $definitionsByKey->has($key)
                && ! $allowedAttributeLookup->has(
                    (string) $key
                )
            ) {
                $validator->errors()->add(
                    "attributes.{$key}",
                    'This field is not a valid active Lead field.'
                );
            }
        }

        foreach ($definitions as $definition) {
            $key = $definition->key;

            $value =
                $attributes[$key] ?? null;

            if (
                $definition->is_required
                && (
                    $value === null
                    || $value === ''
                )
            ) {
                $validator->errors()->add(
                    "attributes.{$key}",
                    "{$definition->name} is required."
                );

                continue;
            }

            if (
                $value === null
                || $value === ''
            ) {
                continue;
            }

            $this->validateDynamicFieldType(
                $validator,
                $definition,
                $value,
            );
        }
    }

    /**
     * Validate the configured dynamic field type.
     */
    private function validateDynamicFieldType(
        Validator $validator,
        LeadFieldDefinition $definition,
        mixed $value,
    ): void {
        $attribute =
            "attributes.{$definition->key}";

        switch ($definition->type) {
            case 'text':
            case 'textarea':
                if (! is_string($value)) {
                    $validator->errors()->add(
                        $attribute,
                        "{$definition->name} must be text."
                    );
                }

                break;

            case 'number':
                if (
                    ! is_int($value)
                    && ! is_float($value)
                    && ! (
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
                $options =
                    $definition->options ?? [];

                if (
                    is_array($options)
                    && $options !== []
                    && ! in_array(
                        $value,
                        $options,
                        true
                    )
                ) {
                    $validator->errors()->add(
                        $attribute,
                        "Please select a valid {$definition->name}."
                    );
                }

                break;

            case 'email':
                if (
                    ! filter_var(
                        $value,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {
                    $validator->errors()->add(
                        $attribute,
                        "{$definition->name} must be a valid email address."
                    );
                }

                break;

            case 'date':
                if (
                    ! is_string($value)
                    || ! strtotime($value)
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
