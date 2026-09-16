<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lead_id',
    'assigned_user_id',
    'type_id',
    'status_id',
    'title',
    'notes',
    'due_at',
    'completed_at',
    'completed_by',
])]
class LeadFollowUp extends Model
{
    use HasFactory;

    /**
     * Lead associated with this follow-up.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * User responsible for the follow-up.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Follow-up type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LeadFollowUpType::class, 'type_id');
    }

    /**
     * Follow-up status.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadFollowUpStatus::class, 'status_id');
    }

    /**
     * User who completed the follow-up.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}