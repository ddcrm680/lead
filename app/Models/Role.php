<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'is_active',
])]
class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Users assigned to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Permissions assigned to this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    /**
     * Check whether this role has a permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()
            ->where('slug', $permission)
            ->exists();
    }

    /**
     * Check whether this role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()
            ->whereIn('slug', $permissions)
            ->exists();
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