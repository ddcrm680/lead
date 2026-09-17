<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListLeads
{
    public function handle(array $validated): LengthAwarePaginator
    {
        $perPage = $validated['per_page'] ?? 25;

        return Lead::query()
            ->with([
                'source:id,name',
                'status:id,name,color_code',
                'pipelineStage:id,name,color_code',
                'assignedUser:id,name',
                'contacts:id,lead_id,type,value,is_primary',
                'tags:id,name',
                'followUps' => fn ($query) => $query
                    ->with([
                        'type:id,name',
                        'status:id,name',
                    ])
                    ->select([
                        'id',
                        'lead_id',
                        'type_id',
                        'status_id',
                        'due_at',
                        'completed_at',
                    ]),
            ])
            ->when(
                !empty($validated['search']),
                function ($query) use ($validated) {
                    $search = trim($validated['search']);

                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('display_name', 'like', "%{$search}%")
                            ->orWhere('public_id', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhereHas('contacts', function ($contactQuery) use ($search) {
                                $contactQuery
                                    ->where('value', 'like', "%{$search}%")
                                    ->orWhere(
                                        'normalized_value',
                                        'like',
                                        "%{$search}%"
                                    );
                            });
                    });
                }
            )
            ->when(
                !empty($validated['status_id']),
                fn ($query) =>
                    $query->where('status_id', $validated['status_id'])
            )
            ->when(
                !empty($validated['source_id']),
                fn ($query) =>
                    $query->where('source_id', $validated['source_id'])
            )
            ->when(
                !empty($validated['city']),
                fn ($query) =>
                    $query->where('city', 'like', '%' . trim($validated['city']) . '%')
            )
            ->when(
                !empty($validated['state']),
                fn ($query) =>
                    $query->where('state', 'like', '%' . trim($validated['state']) . '%')
            )
            ->when(
                !empty($validated['country']),
                fn ($query) =>
                    $query->where('country', 'like', '%' . trim($validated['country']) . '%')
            )
            ->when(
                !empty($validated['pipeline_stage_id']),
                fn ($query) =>
                    $query->where('pipeline_stage_id', $validated['pipeline_stage_id'])
            )
            ->when(
                !empty($validated['assigned_user_id']),
                fn ($query) =>
                    $query->where('assigned_user_id', $validated['assigned_user_id'])
            )
            ->when(
                isset($validated['priority']) && $validated['priority'] !== '',
                fn ($query) =>
                    $query->where('priority', $validated['priority'])
            )
            ->when(
                !empty($validated['tag_ids']),
                function ($query) use ($validated) {
                    foreach ($validated['tag_ids'] as $tagId) {
                        $query->whereHas(
                            'tags',
                            fn ($tagQuery) =>
                                $tagQuery->where('tags.id', $tagId)
                        );
                    }
                }
            )
            ->when(
                !empty($validated['follow_up_type_id']),
                fn ($query) =>
                    $query->whereHas(
                        'followUps',
                        fn ($followUpQuery) =>
                            $followUpQuery->where(
                                'type_id',
                                $validated['follow_up_type_id']
                            )
                    )
            )
            ->when(
                !empty($validated['follow_up_status_id']),
                fn ($query) =>
                    $query->whereHas(
                        'followUps',
                        fn ($followUpQuery) =>
                            $followUpQuery->where(
                                'status_id',
                                $validated['follow_up_status_id']
                            )
                    )
            )
            ->when(
                !empty($validated['created_after']),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $validated['created_after']
                    )
            )
            ->when(
                !empty($validated['created_before']),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $validated['created_before']
                    )
            )
            ->latest()
            ->paginate($perPage);
    }
}
