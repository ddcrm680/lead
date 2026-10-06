<?php

namespace App\Actions\Leads\Import;

use App\Enums\LeadPriority;
use App\Models\LeadFieldDefinition;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Pipeline;
use App\Models\User;

class GetLeadImportOptions
{
    /**
     * Get the current CMS options needed by Lead Import.
     */
    public function handle(): array
    {
        $statuses = LeadStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get([
                'id',
                'name',
            ])
            ->map(fn ($status) => [
                'value' => (string) $status->id,
                'label' => $status->name,
            ])
            ->values()
            ->all();

        $sources = LeadSource::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ])
            ->map(fn ($source) => [
                'value' => (string) $source->id,
                'label' => $source->name,
            ])
            ->values()
            ->all();

        $pipelines = Pipeline::query()
            ->where('is_active', true)
            ->with([
                'stages' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->select([
                        'id',
                        'pipeline_id',
                        'name',
                    ]),
            ])
            ->orderBy('sort_order')
            ->get([
                'id',
                'name',
            ]);

        $pipelineStages = $pipelines
            ->flatMap(
                fn ($pipeline) => $pipeline->stages->map(
                    fn ($stage) => [
                        'value' =>
                            (string) $stage->id,

                        'label' =>
                            $stage->name,

                        'pipeline_id' =>
                            (string) $pipeline->id,

                        'pipeline_name' =>
                            $pipeline->name,
                    ]
                )
            )
            ->values()
            ->all();

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ])
            ->map(fn ($user) => [
                'value' => (string) $user->id,
                'label' => $user->name,
            ])
            ->values()
            ->all();

        $priorities = collect(
            LeadPriority::cases()
        )
            ->map(fn ($priority) => [
                'value' =>
                    (string) $priority->value,

                'label' =>
                    $priority->label(),
            ])
            ->values()
            ->all();

        $fieldDefinitions =
            LeadFieldDefinition::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get([
                    'id',
                    'name',
                    'key',
                    'type',
                    'options',
                    'validation_rules',
                    'is_required',
                ])
                ->map(fn ($field) => [
                    'id' =>
                        $field->id,

                    'name' =>
                        $field->name,

                    'key' =>
                        $field->key,

                    'type' =>
                        $field->type,

                    'options' =>
                        $field->options,

                    'validation_rules' =>
                        $field->validation_rules,

                    'is_required' =>
                        $field->is_required,
                ])
                ->values()
                ->all();

        return [
            'statuses' =>
                $statuses,

            'sources' =>
                $sources,

            'pipeline_stages' =>
                $pipelineStages,

            'users' =>
                $users,

            'priorities' =>
                $priorities,

            'default_priority' =>
                (string) LeadPriority::NORMAL->value,

            'field_definitions' =>
                $fieldDefinitions,
        ];
    }
}
