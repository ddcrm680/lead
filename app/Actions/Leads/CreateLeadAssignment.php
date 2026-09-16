<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadAssignment;

class CreateLeadAssignment
{
    /**
     * Create an assignment history record for a Lead.
     */
    public function handle(
        Lead $lead,
        array $data,
    ): LeadAssignment {
        return $lead->assignments()->create([
            'user_id' => $data['user_id'],
            'assigned_by' => $data['assigned_by'] ?? null,
            'type' => $data['type'] ?? 'manual',
            'reason' => $data['reason'] ?? null,
            'assigned_at' => $data['assigned_at'] ?? now(),
            'ended_at' => $data['ended_at'] ?? null,
        ]);
    }
}