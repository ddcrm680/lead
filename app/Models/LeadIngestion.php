<?php

namespace App\Models;

use App\Enums\LeadIngestionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lead_id',
    'source_id',
    'channel',
    'external_reference',
    'payload',
    'status',
    'error_message',
    'processed_at',
])]
class LeadIngestion extends Model
{
    use HasFactory;

    /**
     * Lead created or updated from this ingestion.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Lead source associated with this ingestion.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status' => LeadIngestionStatus::class,
            'processed_at' => 'datetime',
        ];
    }
}