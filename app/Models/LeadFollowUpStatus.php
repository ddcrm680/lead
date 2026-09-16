<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'key',
    'color_code',
    'description',
    'is_active',
    'is_open',
    'is_completed',
    'is_cancelled',
    'sort_order',
])]
class LeadFollowUpStatus extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_open' => 'boolean',
            'is_completed' => 'boolean',
            'is_cancelled' => 'boolean',
        ];
    }
}