<?php

namespace Database\Seeders;

use App\Models\LeadFollowUpStatus;
use App\Models\LeadFollowUpType;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class LeadDefaultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedLeadSources();
        $this->seedLeadStatuses();

        $pipeline = $this->seedDefaultPipeline();

        $this->seedPipelineStages($pipeline);

        $this->seedFollowUpTypes();
        $this->seedFollowUpStatuses();

        $this->seedTags();
    }

    /**
     * Seed default Lead sources.
     */
    private function seedLeadSources(): void
    {
        $sources = [
            [
                'name' => 'Website',
                'key' => 'website',
                'is_active' => true,
            ],
            [
                'name' => 'Manual',
                'key' => 'manual',
                'is_active' => true,
            ],
            [
                'name' => 'Referral',
                'key' => 'referral',
                'is_active' => true,
            ],
            [
                'name' => 'Import',
                'key' => 'import',
                'is_active' => true,
            ],
            [
                'name' => 'API',
                'key' => 'api',
                'is_active' => true,
            ],
        ];

        foreach ($sources as $source) {
            LeadSource::updateOrCreate(
                ['key' => $source['key']],
                [
                    'name' => $source['name'],
                    'is_active' => $source['is_active'],
                ],
            );
        }
    }

    /**
     * Seed default Lead statuses.
     */
    private function seedLeadStatuses(): void
    {
        $statuses = [
            [
                'name' => 'New',
                'key' => 'new',
                'color_code' => '#3B82F6',
                'description' => 'Newly created Lead.',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Contacted',
                'key' => 'contacted',
                'color_code' => '#8B5CF6',
                'description' => 'Lead has been contacted.',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Qualified',
                'key' => 'qualified',
                'color_code' => '#10B981',
                'description' => 'Lead has been qualified.',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Unqualified',
                'key' => 'unqualified',
                'color_code' => '#F59E0B',
                'description' => 'Lead does not currently meet qualification criteria.',
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'Converted',
                'key' => 'converted',
                'color_code' => '#22C55E',
                'description' => 'Lead has been converted.',
                'is_active' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'Lost',
                'key' => 'lost',
                'color_code' => '#EF4444',
                'description' => 'Lead is no longer active.',
                'is_active' => true,
                'sort_order' => 60,
            ],
        ];

        foreach ($statuses as $status) {
            LeadStatus::updateOrCreate(
                ['key' => $status['key']],
                $status,
            );
        }
    }

    /**
     * Seed the default sales pipeline.
     */
    private function seedDefaultPipeline(): Pipeline
    {
        return Pipeline::updateOrCreate(
            ['key' => 'default-sales'],
            [
                'name' => 'Default Sales Pipeline',
                'description' => 'Default sales pipeline for new installations.',
                'is_active' => true,
                'sort_order' => 10,
            ],
        );
    }

    /**
     * Seed stages for the default sales pipeline.
     */
    private function seedPipelineStages(Pipeline $pipeline): void
    {
        $stages = [
            [
                'name' => 'New',
                'key' => 'new',
                'color_code' => '#3B82F6',
                'description' => 'Newly created Lead.',
                'sort_order' => 10,
            ],
            [
                'name' => 'Contacted',
                'key' => 'contacted',
                'color_code' => '#8B5CF6',
                'description' => 'Lead has been contacted.',
                'sort_order' => 20,
            ],
            [
                'name' => 'Interested',
                'key' => 'interested',
                'color_code' => '#06B6D4',
                'description' => 'Lead has shown interest.',
                'sort_order' => 30,
            ],
            [
                'name' => 'Qualified',
                'key' => 'qualified',
                'color_code' => '#10B981',
                'description' => 'Lead has been qualified.',
                'sort_order' => 40,
            ],
            [
                'name' => 'Proposal Sent',
                'key' => 'proposal-sent',
                'color_code' => '#F59E0B',
                'description' => 'Proposal has been sent to the Lead.',
                'sort_order' => 50,
            ],
            [
                'name' => 'Negotiation',
                'key' => 'negotiation',
                'color_code' => '#F97316',
                'description' => 'Lead is in negotiation.',
                'sort_order' => 60,
            ],
            [
                'name' => 'Won',
                'key' => 'won',
                'color_code' => '#22C55E',
                'description' => 'Lead has been successfully converted.',
                'sort_order' => 70,
            ],
            [
                'name' => 'Lost',
                'key' => 'lost',
                'color_code' => '#EF4444',
                'description' => 'Lead is no longer active.',
                'sort_order' => 80,
            ],
        ];

        foreach ($stages as $stage) {
            PipelineStage::updateOrCreate(
                [
                    'pipeline_id' => $pipeline->id,
                    'key' => $stage['key'],
                ],
                [
                    'name' => $stage['name'],
                    'color_code' => $stage['color_code'],
                    'description' => $stage['description'],
                    'sort_order' => $stage['sort_order'],
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * Seed default Follow-Up types.
     */
    private function seedFollowUpTypes(): void
    {
        $types = [
            [
                'name' => 'Call',
                'key' => 'call',
                'color_code' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Email',
                'key' => 'email',
                'color_code' => '#8B5CF6',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'WhatsApp',
                'key' => 'whatsapp',
                'color_code' => '#22C55E',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Meeting',
                'key' => 'meeting',
                'color_code' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 40,
            ],
        ];

        foreach ($types as $type) {
            LeadFollowUpType::updateOrCreate(
                ['key' => $type['key']],
                $type,
            );
        }
    }

    /**
     * Seed default Follow-Up statuses.
     */
    private function seedFollowUpStatuses(): void
    {
        $statuses = [
            [
                'name' => 'Pending',
                'key' => 'pending',
                'color_code' => '#F59E0B',
                'description' => 'Follow-Up is scheduled and waiting to be completed.',
                'is_active' => true,
                'is_open' => true,
                'is_completed' => false,
                'is_cancelled' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Ongoing',
                'key' => 'ongoing',
                'color_code' => '#3B82F6',
                'description' => 'Follow-Up is currently in progress.',
                'is_active' => true,
                'is_open' => true,
                'is_completed' => false,
                'is_cancelled' => false,
                'sort_order' => 20,
            ],
            [
                'name' => 'Completed',
                'key' => 'completed',
                'color_code' => '#22C55E',
                'description' => 'Follow-Up has been completed.',
                'is_active' => true,
                'is_open' => false,
                'is_completed' => true,
                'is_cancelled' => false,
                'sort_order' => 30,
            ],
            [
                'name' => 'Cancelled',
                'key' => 'cancelled',
                'color_code' => '#EF4444',
                'description' => 'Follow-Up has been cancelled.',
                'is_active' => true,
                'is_open' => false,
                'is_completed' => false,
                'is_cancelled' => true,
                'sort_order' => 40,
            ],
        ];

        foreach ($statuses as $status) {
            LeadFollowUpStatus::updateOrCreate(
                ['key' => $status['key']],
                $status,
            );
        }
    }

    /**
     * Seed default Lead tags.
     */
    private function seedTags(): void
    {
        $tags = [
            [
                'name' => 'Hot',
                'key' => 'hot',
                'color_code' => '#EF4444',
                'is_active' => true,
            ],
            [
                'name' => 'Warm',
                'key' => 'warm',
                'color_code' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'Cold',
                'key' => 'cold',
                'color_code' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'VIP',
                'key' => 'vip',
                'color_code' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'Follow Up',
                'key' => 'follow-up',
                'color_code' => '#10B981',
                'is_active' => true,
            ],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['key' => $tag['key']],
                [
                    'name' => $tag['name'],
                    'color_code' => $tag['color_code'],
                    'is_active' => $tag['is_active'],
                ],
            );
        }
    }



}