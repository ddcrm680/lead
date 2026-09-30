<?php

namespace App\Exports;

use App\Actions\Leads\ListLeads;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;

class LeadsExport
{

    protected int $serial = 0;

    public function __construct(
        protected array $filters,
        protected ListLeads $listLeads,
    ) {
    }

    /**
     * Get Lead contact values by type.
     */
    protected function contactValues(
        Lead $lead,
        string $type,
    ): string {
        return $lead->contacts
            ->where('type', $type)
            ->pluck('value')
            ->filter()
            ->implode(', ');
    }

    /**
     * Build the leads export query.
     */
    public function query(): Builder
    {
        return $this->listLeads
            ->query($this->filters)
            ->latest();
    }

    /**
     * Convert a lead into an export row.
     */
    public function row(Lead $lead): array
    {
        return [
            'Sr. No' => ++$this->serial,
            'Name' => $lead->display_name,

            'Phone' => $this->contactValues(
                $lead,
                'phone'
            ),

            'WhatsApp' => $this->contactValues(
                $lead,
                'whatsapp'
            ),

            'Email' => $this->contactValues(
                $lead,
                'email'
            ),

            'City' => $lead->city ?? '',
            'State' => $lead->state ?? '',
            'Country' => $lead->country ?? '',

            'Status' => $lead->status?->name ?? '',
            'Stage' => $lead->pipelineStage?->name ?? '',
            'Source' => $lead->source?->name ?? '',
            'Assigned To' => $lead->assignedUser?->name ?? 'Unassigned',

            'Priority' => $lead->priority?->label() ?? '',

            'Tags' => $lead->tags
                ->pluck('name')
                ->implode(', '),

            // 'Latest Note' =>
            //     $lead->latestNote?->payload['notes'] ?? '',

            'Created At' =>
                $lead->created_at?->format('Y-m-d H:i:s'),

            'Last Updated' =>
                $lead->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Return the export title.
     */
    public function title(): string
    {
        return 'Leads Report';
    }

    /**
     * Return the export filename.
     */
    public function filename(string $format = 'csv'): string
    {
        return 'leads_' . now()->format('Ymd_His') . ".{$format}";
    }
}
