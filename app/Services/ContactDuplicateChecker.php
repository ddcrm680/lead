<?php

namespace App\Services;

use App\Models\Lead;

class ContactDuplicateChecker
{
    /**
     * Determine whether a contact already exists for the Lead.
     */
    public function exists(
        Lead $lead,
        string $type,
        string $normalizedValue,
        ?int $ignoreContactId = null,
    ): bool {
        return $lead->contacts()
            ->where('type', $type)
            ->where('normalized_value', $normalizedValue)
            ->when($ignoreContactId, fn ($query) => $query->where('id', '!=', $ignoreContactId))
            ->exists();
    }
}