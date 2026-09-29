<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Support\Facades\DB;

class UpdateLeadStatus
{
    public function __construct(
        private CreateLeadEvent $createLeadEvent,
    ) {
    }

    public function handle(
        Lead $lead,
        LeadStatus $status,
        array $data,
        ?int $updatedBy = null,
    ): Lead {
        return DB::transaction(function () use (
            $lead,
            $status,
            $data,
            $updatedBy,
        ) {
            $previousStatus = $lead->status;

            if ((int) $lead->status_id === (int) $status->id) {
                return $lead;
            }

            $lead->update([
                'status_id' => $status->id,
            ]);

            $this->createLeadEvent->handle(
                $lead,
                [
                    'user_id' => $updatedBy,
                    'type' => 'status_changed',
                    'title' => 'Lead status changed',
                    'description' => sprintf(
                        '%s → %s',
                        $previousStatus?->name ?? 'Unknown',
                        $status->name,
                    ),
                    'payload' => [
                        'previous_status_id' => $previousStatus?->id,
                        'new_status_id' => $status->id,
                        'notes' => $data['notes'] ?? null,
                    ],
                ],
            );

            $lead->setRelation(
                'status',
                $status,
            );

            return $lead;
        });
    }
}
