<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'slug',
    'description',
    'assignment_method',
    'is_active',
])]


class LeadGroup extends Model
{
    use HasFactory, SoftDeletes;

    public function userLeadGroups()
    {
        return $this->hasMany(UserLeadGroup::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_lead_groups')
            ->withPivot([
                'is_active',
                'sort_order',
            ])
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}