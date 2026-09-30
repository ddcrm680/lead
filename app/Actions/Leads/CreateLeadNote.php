<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\LeadEvent;

class CreateLeadNote
{
    public function __construct(
        private CreateLeadEvent $createLeadEvent,
    ) {
    }

    public function handle(
        Lead $lead,
        array $data,
        ?int $createdBy = null,
    ): LeadEvent {
        return $this->createLeadEvent->handle(
            $lead,
            [
                'user_id' => $createdBy,
                'type' => 'note_added',
                'title' => 'Note added',
                'payload' => [
                    'notes' => $data['note'],
                ],
            ],
        );
    }
}
