<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UsersExport
{
    public function __construct(
        protected Request $request
    ) {
    }

    /**
     * Build the users query.
     */
    public function query(): Builder
    {
        return User::query()
            ->with('role:id,name,slug')
            ->when($this->request->filled('search'), function ($query) {
                $search = trim($this->request->input('search'));

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when(
                $this->request->filled('role_id'),
                fn ($query) => $query->where(
                    'role_id',
                    $this->request->input('role_id')
                )
            )
            ->when(
                $this->request->filled('status'),
                fn ($query) => $query->where(
                    'is_active',
                    $this->request->boolean('status')
                )
            )
            ->orderByDesc('is_active')
            ->latest();
    }

    /**
     * Convert a user into an export row.
     */
    public function row(User $user): array
    {
        return [
            'Name' => $user->name,
            'Email' => $user->email,
            'Phone' => $user->phone
                ? trim(($user->phone_country_code ?? '') . ' ' . $user->phone)
                : '',
            'Address' => $user->address ?? '',
            'Role' => $user->role?->name ?? 'No role',
            'Status' => $user->is_active ? 'ACTIVE' : 'INACTIVE',
            'Created At' => $user->created_at?->format('Y-m-d H:i:s'),
            'Last Updated' => $user->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Return the export title.
     */
    public function title(): string
    {
        return 'Users Report';
    }

    public function filename(string $format = 'csv'): string
    {
        return 'users_' . now()->format('Ymd_His') . ".{$format}";
    }
}