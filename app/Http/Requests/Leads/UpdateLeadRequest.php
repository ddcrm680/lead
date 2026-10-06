<?php

namespace App\Http\Requests\Leads;

use App\Services\LeadPayloadValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
     * Update uses the shared Lead payload rules and adds
     * only the existing-contact ID used by the edit flow.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(
        LeadPayloadValidator $leadPayloadValidator,
    ): array {
        return [
            ...$leadPayloadValidator->rules(),

            'contacts.*.id' => [
                'nullable',
                'integer',
            ],
        ];
    }

    /**
     * Attach shared Lead-domain validation plus the
     * edit-form duplicate contact check.
     */
    public function withValidator(
        Validator $validator,
    ): void {
        app(LeadPayloadValidator::class)->after(
            validator: $validator,
            data: $this->all(),
        );

        $validator->after(function (
            Validator $validator
        ): void {
            $this->validateIncomingContactDuplicates(
                $validator
            );
        });
    }

    /**
     * Preserve the existing edit-form behavior that catches
     * the same contact entered more than once before UpdateLead
     * begins reconciling existing contacts.
     */
    private function validateIncomingContactDuplicates(
        Validator $validator,
    ): void {
        $contacts =
            $this->input(
                'contacts',
                []
            );

        if (! is_array($contacts)) {
            return;
        }

        $seenContacts = [];

        foreach (
            $contacts
            as $index => $contact
        ) {
            if (! is_array($contact)) {
                continue;
            }

            $type =
                (string) (
                    $contact['type']
                    ?? ''
                );

            $value =
                trim(
                    (string) (
                        $contact['value']
                        ?? ''
                    )
                );

            if (
                $type === ''
                || $value === ''
            ) {
                continue;
            }

            $cleanValue =
                strtolower(
                    preg_replace(
                        '/\s+/',
                        '',
                        $value
                    ) ?? ''
                );

            $pair =
                "{$type}:{$cleanValue}";

            if (isset($seenContacts[$pair])) {
                $validator->errors()->add(
                    "contacts.{$index}.value",
                    "This {$type} contact was entered multiple times in the form."
                );
            }

            $seenContacts[$pair] = true;
        }
    }
}
