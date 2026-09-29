<?php

namespace App\Actions\Leads;

use App\Models\Lead;

class DeleteLead
{
    /**
     * Soft delete the specified lead.
     */
    public function handle(Lead $lead): void
    {
        $lead->delete();
    }
}
