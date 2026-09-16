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
    ): bool {
        return $lead->contacts()
            ->where('type', $type)
            ->where('normalized_value', $normalizedValue)
            ->exists();
    }
}