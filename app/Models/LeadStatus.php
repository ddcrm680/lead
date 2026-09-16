<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'key',
    'color_code',
    'description',
    'is_active',
    'sort_order',
])]
class LeadStatus extends Model
{
    use HasFactory;

    /**
     * Leads using this status.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'status_id');
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