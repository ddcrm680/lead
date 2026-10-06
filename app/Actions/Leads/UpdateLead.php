<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateLead
{
    public function __construct(
        private CreateLeadContact $createLeadContact,
        private UpdateLeadContact $updateLeadContact,
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
        return DB::transaction(function () use (
            $lead,
            $data,
            $updatedBy,
        ) {
            /*
             * 1. Capture assignment before updating.
             */
            $previousAssignedUserId =
                $lead->assigned_user_id
                    ? (int) $lead->assigned_user_id
                    : null;

            $newAssignedUserId =
                ! empty($data['assigned_user_id'])
                    ? (int) $data['assigned_user_id']
                    : null;

            /*
             * 2. Update Lead attributes.
             */
            $lead->update([
                'display_name' => $data['display_name'],
                'source_id' => $data['source_id'] ?? null,
                'status_id' => $data['status_id'],
                'pipeline_stage_id' =>
                    $data['pipeline_stage_id'] ?? null,
                'assigned_user_id' =>
                    $newAssignedUserId,
                'priority' =>
                    ! empty($data['priority'])
                        ? (int) $data['priority']
                        : 20,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'attributes' =>
                    $data['attributes'] ?? null,
            ]);

            /*
             * 3. Handle assignment changes.
             */
            if (
                $previousAssignedUserId !==
                $newAssignedUserId
            ) {
                if ($previousAssignedUserId) {
                    $lead->assignments()
                        ->whereNull('ended_at')
                        ->update([
                            'ended_at' => now(),
                        ]);
                }

                if ($newAssignedUserId) {
                    $this->createLeadAssignment->handle(
                        $lead,
                        [
                            'user_id' =>
                                $newAssignedUserId,
                            'assigned_by' =>
                                $updatedBy,
                            'type' => 'manual',
                            'reason' =>
                                'Lead reassigned via edit',
                            'assigned_at' =>
                                now(),
                        ],
                    );
                }
            }

            /*
             * 4. Reconcile Lead contacts.
             */
            $submittedContacts =
                $data['contacts'] ?? [];

            $retainedContactIds = [];

            $existingContacts =
                collect();

            /*
             * Resolve and verify all submitted existing
             * contacts before deleting or updating anything.
             */
            foreach (
                $submittedContacts as $contactData
            ) {
                if (
                    empty($contactData['id'])
                ) {
                    continue;
                }

                $contactId =
                    (int) $contactData['id'];

                $contact =
                    $lead->contacts()
                        ->whereKey($contactId)
                        ->first();

                if (! $contact) {
                    throw ValidationException::withMessages([
                        'contacts' => [
                            'One or more specified contacts do not belong to this Lead.',
                        ],
                    ]);
                }

                $retainedContactIds[] =
                    $contactId;

                $existingContacts->put(
                    $contactId,
                    $contact,
                );
            }

            /*
             * Delete contacts removed from the edit form.
             */
            $lead->contacts()
                ->whereNotIn(
                    'id',
                    $retainedContactIds
                )
                ->delete();

            /*
             * Update existing contacts through
             * UpdateLeadContact and create new contacts
             * through CreateLeadContact.
             */
            foreach (
                $submittedContacts as $index => $contactData
            ) {
                $contactId =
                    ! empty($contactData['id'])
                        ? (int) $contactData['id']
                        : null;

                $errorKey =
                    "contacts.{$index}.value";

                if ($contactId) {
                    $contact =
                        $existingContacts->get(
                            $contactId
                        );

                    if (! $contact) {
                        throw ValidationException::withMessages([
                            $errorKey => [
                                'This contact does not belong to the specified Lead.',
                            ],
                        ]);
                    }

                    $this->updateLeadContact->handle(
                        lead: $lead,
                        contact: $contact,
                        data: $contactData,
                        errorKey: $errorKey,
                    );

                    continue;
                }

                $this->createLeadContact->handle(
                    lead: $lead,
                    data: $contactData,
                    errorKey: $errorKey,
                );
            }

            /*
             * 5. Sync Tags.
             */
            $tags =
                $data['tags']
                ?? $data['tag_ids']
                ?? [];

            $this->syncLeadTags->handle(
                $lead,
                $tags,
            );

            /*
             * 6. Record update event.
             */
            $this->createLeadEvent->handle(
                $lead,
                [
                    'user_id' => $updatedBy,
                    'type' => 'updated',
                    'title' => 'Lead updated',
                    'description' =>
                        'Lead details and information were updated.',
                    'occurred_at' => now(),
                ],
            );

            return $lead->fresh();
        });
    }
}
