<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadContact;
use App\Services\ContactDuplicateChecker;
use App\Services\ContactNormalizer;
use Illuminate\Validation\ValidationException;

class CreateLeadContact
{
    public function __construct(
        private ContactNormalizer $contactNormalizer,
        private ContactDuplicateChecker $contactDuplicateChecker,
    ) {
    }

    /**
     * Create a contact method for a Lead.
     */
    public function handle(
        Lead $lead,
        array $data,
        string $errorKey = 'contacts',
    ): LeadContact {
        $normalizedValue =
            $this->contactNormalizer->normalize(
                $data['type'],
                $data['value'],
            );

        /*
         * Prevent the same normalized contact from being
         * added more than once to the same Lead.
         */
        if (
            $this->contactDuplicateChecker->exists(
                $lead,
                $data['type'],
                $normalizedValue,
            )
        ) {
            throw ValidationException::withMessages([
                $errorKey => [
                    'This ' . $data['type'] . ' contact already exists for this Lead.',
                ],
            ]);
        }

        /*
         * Prevent the same normalized contact from being
         * used by another Lead from the same source.
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


        return $lead->contacts()->create([
            'type' => $data['type'],
            'value' => $data['value'],
            'normalized_value' => $normalizedValue,
            'is_primary' => $data['is_primary'] ?? false,
            'verified_at' => $data['verified_at'] ?? null,
        ]);
    }
}
