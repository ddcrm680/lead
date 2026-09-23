<?php

namespace App\Actions\Leads;

use App\Models\Lead;

class GetLeadDetails
{
    /**
     * Get a lead with all data required by the full details view.
     */
    public function handle(string $publicId): Lead
    {
        return Lead::query()
            ->where('public_id', $publicId)
            ->with([
                'source',
                'status',
                'pipelineStage',
                'assignedUser',
                'creator',
                'contacts',
                'tags',
                'followUps.type',
                'followUps.status',
                'followUps.assignedUser',
                'followUps.completedBy',
                'assignments.user',
                'assignments.assignedBy',
                'events.user',
            ])
            ->firstOrFail();
    }
}
