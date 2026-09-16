<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\Tag;
use Illuminate\Support\Str;

class SyncLeadTags
{
    /**
     * Synchronize tags assigned to a Lead (handles IDs & dynamic new strings).
     */
    public function handle(
        Lead $lead,
        array $tags = [],
    ): void {
        $tagIds = collect($tags)->map(function ($tag) {
            if (is_numeric($tag)) {
                return (int) $tag;
            }

            $name = trim($tag);
            if (empty($name)) {
                return null;
            }

            // Create new tag if it doesn't already exist
            $newTag = Tag::firstOrCreate(
                ['name' => $name],
                [
                    'key' => Str::slug($name),
                    'color_code' => '#6B7280',
                    'is_active' => true,
                ]
            );

            return $newTag->id;
        })->filter()->all();

        $lead->tags()->sync($tagIds);
    }
}