<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'phone_country_code',
    'phone',
    'address',
    'password',
    'role_id',
    'is_active',
    'avatar',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * User role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->slug, $roles, true);
    }

    /**
     * Check whether the user has a permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role?->hasPermission($permission) ?? false;
    }

    /**
     * Check whether the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->role?->hasAnyPermission($permissions) ?? false;
    }
    
    // lead groups 
    public function userLeadGroups()
    {
        return $this->hasMany(UserLeadGroup::class);
    }

    public function leadGroups()
    {
        return $this->belongsToMany(LeadGroup::class, 'user_lead_groups')
            ->withPivot([
                'is_active',
                'sort_order',
            ])
            ->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}