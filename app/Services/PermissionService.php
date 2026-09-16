<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionService
{
    /**
     * Synchronize application permissions and default role assignments.
     */
    public function sync(): array
    {
        return DB::transaction(function () {

            $permissions = $this->syncPermissions();

            $assignments = $this->ensureRoleDefaults($permissions);

            return [
                'permissions_created' => $permissions['created'],
                'permissions_updated' => $permissions['updated'],
                'role_assignments_created' => $assignments,
            ];
        });
    }

    /**
     * Synchronize permissions defined in config/permissions.php.
     *
     * @return array{
     *     permissions: \Illuminate\Support\Collection,
     *     created: int,
     *     updated: int
     * }
     */
    private function syncPermissions(): array
    {
        $definitions = config('permissions.permissions', []);

        $permissions = collect();
        $created = 0;
        $updated = 0;

        foreach ($definitions as $module => $actions) {

            foreach ($actions as $action) {

                $slug = "{$module}.{$action}";

                $name = $this->makePermissionName($module, $action);

                $description = $this->makePermissionDescription(
                    $module,
                    $action
                );

                $permission = Permission::where('slug', $slug)->first();

                if (!$permission) {
                    $permission = Permission::create([
                        'name' => $name,
                        'slug' => $slug,
                        'module' => $module,
                        'description' => $description,
                    ]);

                    $created++;
                } else {

                    $permission->update([
                        'name' => $name,
                        'module' => $module,
                        'description' => $description,
                    ]);

                    $updated++;
                }

                $permissions->put($slug, $permission);
            }
        }

        return [
            'permissions' => $permissions,
            'created' => $created,
            'updated' => $updated,
        ];
    }

    /**
     * Add configured default permissions to roles.
     *
     * Existing assignments are preserved.
     */
    private function ensureRoleDefaults(array $permissions): int
    {
        $roleDefaults = config('permissions.role_defaults', []);

        $assignmentsCreated = 0;

        foreach ($roleDefaults as $roleSlug => $permissionSlugs) {

            $role = Role::where('slug', $roleSlug)->first();

            if (!$role) {
                continue;
            }

            if ($permissionSlugs === '*') {
                $permissionIds = $permissions['permissions']
                    ->pluck('id')
                    ->all();
            } else {
                $permissionIds = collect($permissionSlugs)
                    ->map(fn ($slug) => $permissions['permissions']->get($slug)?->id)
                    ->filter()
                    ->values()
                    ->all();
            }

            foreach ($permissionIds as $permissionId) {

                $exists = $role->permissions()
                    ->whereKey($permissionId)
                    ->exists();

                if (!$exists) {
                    $role->permissions()->attach($permissionId);

                    $assignmentsCreated++;
                }
            }
        }

        return $assignmentsCreated;
    }

    /**
     * Generate a human-readable permission name.
     */
    private function makePermissionName(string $module, string $action): string
    {
        return Str::headline("{$action} {$module}");
    }

    /**
     * Generate a default permission description.
     */
    private function makePermissionDescription(
        string $module,
        string $action
    ): string {
        return Str::lower(
            Str::headline("{$action} {$module}")
        ) . '.';
    }
}