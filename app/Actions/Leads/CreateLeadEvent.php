<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadEvent;

class CreateLeadEvent
{
    /**
     * Create an event in the Lead history.
     */
    public function handle(
        Lead $lead,
        array $data,
    ): LeadEvent {
        return $lead->events()->create([
            'user_id' => $data['user_id'] ?? null,
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'payload' => $data['payload'] ?? null,
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);
    }
}