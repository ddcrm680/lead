<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadContact;
use App\Services\ContactDuplicateChecker;
use App\Services\ContactNormalizer;
use Illuminate\Validation\ValidationException;

class UpdateLeadContact
{
    public function __construct(
        private ContactNormalizer $contactNormalizer,
        private ContactDuplicateChecker $contactDuplicateChecker,
    ) {
    }

    /**
     * Update an existing contact belonging to a Lead.
     */
    public function handle(
        Lead $lead,
        LeadContact $contact,
        array $data,
        string $errorKey = 'contacts',
    ): LeadContact {
        if (
            (int) $contact->lead_id !==
            (int) $lead->id
        ) {
            throw ValidationException::withMessages([
                $errorKey => [
                    'This contact does not belong to the specified Lead.',
                ],
            ]);
        }

        $normalizedValue =
            $this->contactNormalizer->normalize(
                $data['type'],
                $data['value'],
            );

        /*
         * Ignore the contact currently being updated,
         * but reject another matching contact on this Lead.
         */
        if (
            $this->contactDuplicateChecker->exists(
                $lead,
                $data['type'],
                $normalizedValue,
                $contact->id,
            )
        ) {
            throw ValidationException::withMessages([
                $errorKey => [
                    'This ' . $data['type'] . ' contact already exists for this Lead.',
                ],
            ]);
        }

        /*
         * Reject the same normalized contact when another
         * Lead already has it under the same source.
         */
        if (
            $this->contactDuplicateChecker->existsForSource(
                $lead->source_id
                    ? (int) $lead->source_id
                    : null,
                $data['type'],
                $normalizedValue,
                $lead->id,
            )
        ) {
            throw ValidationException::withMessages([
                $errorKey => [
                    'A Lead with the same source and '
                    . $data['type']
                    . ' contact already exists.',
                ],
            ]);
        }

        $contact->update([
            'type' => $data['type'],
            'value' => $data['value'],
            'normalized_value' => $normalizedValue,
            'is_primary' => ! empty(
                $data['is_primary']
            ),
        ]);

        return $contact->refresh();
    }
}
