<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateGeneralSettingsRequest;
use App\Http\Requests\Settings\UpdateRolePermissionsRequest;

use App\Models\Permission;
use App\Models\Role;
use App\Services\SettingsService;
use Illuminate\Http\Request;


class SettingsController extends Controller
{
    /**
     * Display workspace settings.
     */
    public function index(Request $request, SettingsService $settingsService)
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('module')
            ->orderBy('name')
            ->get();

        $generalSettings = $settingsService->getGroup('general');

        return view('pages.settings.index', compact('roles', 'permissions', 'generalSettings'));

    }

    /**
     * Update general workspace settings.
     */
    public function updateGeneral(UpdateGeneralSettingsRequest $request, SettingsService $settingsService)
    {
        

        $settings = $request->validated();
        $logo = $settings['workspace_logo'] ?? null;
        $favicon = $settings['workspace_favicon'] ?? null;

        unset(
            $settings['workspace_logo'],
            $settings['workspace_favicon']
        );

        $settingsService->update('general', $settings);

        if ($logo) {
            $settingsService->updateFile(
                'general',
                'workspace_logo',
                $logo,
                'images/workspace',
                'logo'
            );
        }

        if ($favicon) {
            $settingsService->updateFile(
                'general',
                'workspace_favicon',
                $favicon,
                'images/workspace',
                'favicon'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'General settings have been updated successfully.',
        ]);
    }

     /**
     * Update permissions assigned to a role.
     */
    public function updateRolePermissions(UpdateRolePermissionsRequest $request, Role $role)
    {

        if ($role->slug === 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'The Super Administrator role cannot be modified.',
            ], 403);
        }

        $permissionIds = $request->validated('permission_ids') ?? [];

        $role->permissions()->sync($permissionIds);

        return response()->json([
            'success' => true,
            'message' => "Permissions for {$role->name} have been updated successfully.",
        ]);
    }

}