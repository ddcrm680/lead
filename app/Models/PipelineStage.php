<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'pipeline_id',
    'name',
    'key',
    'color_code',
    'description',
    'sort_order',
    'is_active',
])]
class PipelineStage extends Model
{
    use HasFactory;

    /**
     * Pipeline this stage belongs to.
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    /**
     * Leads currently in this stage.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'pipeline_stage_id');
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}