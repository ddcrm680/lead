<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Services\ContactDuplicateChecker;
use App\Services\ContactNormalizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateLead
{
    public function __construct(
        private ContactNormalizer $contactNormalizer,
        private ContactDuplicateChecker $contactDuplicateChecker,
        private CreateLeadAssignment $createLeadAssignment,
        private SyncLeadTags $syncLeadTags,
        private CreateLeadEvent $createLeadEvent,
    ) {
    }

    /**
     * Update a Lead and its related data atomically.
     */
    public function handle(
        Lead $lead,
        array $data,
        ?int $updatedBy = null,
    ): Lead {
        return DB::transaction(function () use ($lead, $data, $updatedBy) {
            // 1. Capture previous assigned_user_id BEFORE updating the Lead
            $previousAssignedUserId = $lead->assigned_user_id ? (int) $lead->assigned_user_id : null;
            $newAssignedUserId = !empty($data['assigned_user_id']) ? (int) $data['assigned_user_id'] : null;

            // 2. Update Lead attributes
            $lead->update([
                'display_name' => $data['display_name'],
                'source_id' => $data['source_id'] ?? null,
                'status_id' => $data['status_id'],
                'pipeline_stage_id' => $data['pipeline_stage_id'] ?? null,
                'assigned_user_id' => $newAssignedUserId,
                'priority' => !empty($data['priority']) ? (int) $data['priority'] : 20,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'attributes' => $data['attributes'] ?? null,
            ]);

            // 3. Handle assignment changes and history
            if ($previousAssignedUserId !== $newAssignedUserId) {
                if ($previousAssignedUserId) {
                    $lead->assignments()
                        ->whereNull('ended_at')
                        ->update(['ended_at' => now()]);
                }

                if ($newAssignedUserId) {
                    $this->createLeadAssignment->handle(
                        $lead,
                        [
                            'user_id' => $newAssignedUserId,
                            'assigned_by' => $updatedBy,
                            'type' => 'manual',
                            'reason' => 'Lead reassigned via edit',
                            'assigned_at' => now(),
                        ]
                    );
                }
            }

            // 4. Handle Contacts reconciliation with strict ownership verification
            $submittedContacts = $data['contacts'] ?? [];
            $retainedContactIds = [];

            // Ownership check: any provided contact ID MUST belong to this lead
            foreach ($submittedContacts as $contactData) {
                if (!empty($contactData['id'])) {
                    $contactId = (int) $contactData['id'];
                    $ownsContact = $lead->contacts()->where('id', $contactId)->exists();

                    if (!$ownsContact) {
                        throw ValidationException::withMessages([
                            'contacts' => ['One or more specified contacts do not belong to this lead.'],
                        ]);
                    }

                    $retainedContactIds[] = $contactId;
                }
            }

            // Delete contacts belonging to this lead that were removed in the UI
            $lead->contacts()
                ->whereNotIn('id', $retainedContactIds)
                ->delete();

            // Update existing or create newly appended contacts
            foreach ($submittedContacts as $index => $contactData) {
                $type = $contactData['type'];
                $value = $contactData['value'];
                $isPrimary = !empty($contactData['is_primary']);
                $contactId = !empty($contactData['id']) ? (int) $contactData['id'] : null;

                $normalizedValue = $this->contactNormalizer->normalize($type, $value);

                // Check duplicates against other contacts for this lead
                if ($this->contactDuplicateChecker->exists($lead, $type, $normalizedValue, $contactId)) {
                    throw ValidationException::withMessages([
                        "contacts.{$index}.value" => ["This {$type} contact already exists for this lead."],
                    ]);
                }

                if ($contactId) {
                    // Update existing contact (resolved through lead relationship)
                    $lead->contacts()->where('id', $contactId)->update([
                        'type' => $type,
                        'value' => $value,
                        'normalized_value' => $normalizedValue,
                        'is_primary' => $isPrimary,
                    ]);
                } else {
                    // Create new contact
                    $lead->contacts()->create([
                        'type' => $type,
                        'value' => $value,
                        'normalized_value' => $normalizedValue,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }

            // 5. Sync Tags via existing SyncLeadTags
            $tags = $data['tags'] ?? $data['tag_ids'] ?? [];
            $this->syncLeadTags->handle($lead, $tags);

            // 6. Create LeadEvent for the update
            $this->createLeadEvent->handle(
                $lead,
                [
                    'user_id' => $updatedBy,
                    'type' => 'updated',
                    'title' => 'Lead updated',
                    'description' => 'Lead details and information were updated.',
                    'occurred_at' => now(),
                ]
            );

            return $lead->fresh();
        });
    }
}
