<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadFollowUp;
use Illuminate\Support\Facades\DB;

class CreateLeadFollowUp
{
    public function __construct(
        private CreateLeadEvent $createLeadEvent,
    ) {
    }

    public function handle(
        Lead $lead,
        array $data,
        ?int $createdBy = null,
    ): LeadFollowUp {
        return DB::transaction(function () use ($lead, $data, $createdBy) {
            $followUp = $lead->followUps()->create([
                'assigned_user_id' => $data['assigned_user_id'] ?? null,
                'type_id' => $data['type_id'],
                'status_id' => $data['status_id'],
                'title' => $data['title'],
                'notes' => $data['notes'] ?? null,
                'due_at' => $data['due_at'],
            ]);

            $this->createLeadEvent->handle(
                $lead,
                [
                    'user_id' => $createdBy,
                    'type' => 'follow_up_created',
                    'title' => 'Follow-up scheduled',
                    'description' => $followUp->title,
                ],
            );

            return $followUp;
        });
    }
}
