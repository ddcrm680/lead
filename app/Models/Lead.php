<?php

namespace App\Models;

use App\Enums\LeadPriority;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'public_id',
    'display_name',
    'source_id',
    'status_id',
    'pipeline_stage_id',
    'assigned_user_id',
    'created_by',
    'priority',
    'city',
    'state',
    'country',
    'attributes',
])]
class Lead extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Lead source.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    /**
     * Lead status.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }

    /**
     * Current pipeline stage.
     */
    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    /**
     * Assigned user.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }


    /**
     * Lead Creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Contact methods belonging to the Lead.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(LeadContact::class);
    }

    /**
     * Ingestion records associated with the Lead.
     */
    public function ingestions(): HasMany
    {
        return $this->hasMany(LeadIngestion::class);
    }

    /**
     * External references belonging to the Lead.
     */
    public function externalReferences(): HasMany
    {
        return $this->hasMany(LeadExternalReference::class);
    }


    /**
     * Tags assigned to the Lead.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'lead_tag')
            ->withTimestamps();
    }


    /**
     * Assignment history for the Lead.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class);
    }

    /**
     * Events in the Lead history.
     */
    public function events(): HasMany
    {
        return $this->hasMany(LeadEvent::class);
    }

    /**
     * Follow-ups scheduled for the Lead.
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class);
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'priority' => LeadPriority::class,
            'attributes' => 'array',
        ];
    }
}