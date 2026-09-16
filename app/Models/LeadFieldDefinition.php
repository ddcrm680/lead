<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'key',
    'type',
    'options',
    'validation_rules',
    'is_required',
    'is_filterable',
    'is_active',
    'sort_order',
])]
class LeadFieldDefinition extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'validation_rules' => 'array',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}