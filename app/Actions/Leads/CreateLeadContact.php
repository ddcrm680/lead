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
    ): LeadContact {
        $normalizedValue = $this->contactNormalizer->normalize(
            $data['type'],
            $data['value'],
        );

        /*
         * Check the normalized value to prevent the same contact
         * from being added to the same Lead in a different format.
         */
        if ($this->contactDuplicateChecker->exists(
            $lead,
            $data['type'],
            $normalizedValue,
        )) {
            throw ValidationException::withMessages([
                'contacts' => [
                    'This ' . $data['type'] . ' contact already exists for this Lead.',
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