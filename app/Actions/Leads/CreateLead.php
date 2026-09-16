<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateLead
{
    public function __construct(
        private CreateLeadContact $createLeadContact,
        private CreateLeadAssignment $createLeadAssignment,
        private SyncLeadTags $syncLeadTags,
        private CreateLeadEvent $createLeadEvent,
    ) {
    }

    /**
     * Create a Lead and its initial related data atomically.
     */
    public function handle(
        array $data,
        ?int $createdBy = null,
    ): Lead {
        return DB::transaction(function () use ($data, $createdBy) {
            $lead = Lead::create([
                'public_id' => (string) Str::ulid(),
                'display_name' => $data['display_name'],
                'source_id' => $data['source_id'] ?? null,
                'status_id' => $data['status_id'],
                'pipeline_stage_id' => $data['pipeline_stage_id'] ?? null,
                'assigned_user_id' => $data['assigned_user_id'] ?? null,
                'created_by' => $createdBy,
                'priority' => !empty($data['priority']) ? (int) $data['priority'] : 20,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'attributes' => $data['attributes'] ?? null,
            ]);

            foreach ($data['contacts'] ?? [] as $contact) {
                $this->createLeadContact->handle(
                    $lead,
                    $contact,
                );
            }

            if (!empty($data['assigned_user_id'])) {
                $this->createLeadAssignment->handle(
                    $lead,
                    [
                        'user_id' => $data['assigned_user_id'],
                        'assigned_by' => $createdBy,
                        'type' => 'manual',
                    ],
                );
            }

            // Sync predefined and custom Tom Select tags
            $tags = $data['tags'] ?? $data['tag_ids'] ?? [];
            $this->syncLeadTags->handle($lead, $tags);
            
            $this->createLeadEvent->handle(
                $lead,
                [
                    'user_id' => $createdBy,
                    'type' => 'created',
                    'title' => 'Lead created',
                ],
            );

            return $lead;
        });
    }
}