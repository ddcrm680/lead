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

     /**
     * Determine whether the same normalized contact
     * already exists on another Lead with the same source.
     */
    public function existsForSource(
        ?int $sourceId,
        string $type,
        string $normalizedValue,
        ?int $ignoreLeadId = null,
    ): bool {
        return Lead::query()
            ->when(
                $sourceId === null,
                fn ($query) =>
                    $query->whereNull(
                        'source_id'
                    ),
                fn ($query) =>
                    $query->where(
                        'source_id',
                        $sourceId
                    )
            )
            ->when(
                $ignoreLeadId,
                fn ($query) =>
                    $query->where(
                        'id',
                        '!=',
                        $ignoreLeadId
                    )
            )
            ->whereHas(
                'contacts',
                fn ($query) =>
                    $query
                        ->where(
                            'type',
                            $type
                        )
                        ->where(
                            'normalized_value',
                            $normalizedValue
                        )
            )
            ->exists();
    }


}
