<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'key',
    'description',
    'is_active',
    'sort_order',
])]
class Pipeline extends Model
{
    use HasFactory;

    /**
     * Pipeline stages.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class);
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