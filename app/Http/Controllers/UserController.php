<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Http\Requests\Users\ExportUsersRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\UserDataRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Export\ExportService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    
    /**
     * Display a listing of users.
     */
    public function index()
    {

        $roles = Role::orderBy('name')->get();

        return view('pages.users.index', compact('roles'));
    }

    /**
     * Return users for the users directory.
    */
    public function data(UserDataRequest $request)
    {

        $validated = $request->validated();
        $perPage = $validated['per_page'] ?? 25;

        $users = User::query()
            ->with('role:id,name,slug')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->search);

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role_id'), fn ($q) =>
                $q->where('role_id', $request->role_id)
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status)
            )
            ->orderByDesc('is_active')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'html' => view(
                'pages.users.components.user-list',
                compact('users')
            )->render(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, FileUploadService $fileUploadService)
    {
        $validated = $request->validated();

        $role = Role::findOrFail($validated['role_id']);

        if ($role->slug === 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'The Super Administrator role cannot be assigned to another user.',
            ], 403);
        }

        $avatar = $validated['avatar'] ?? null;

        unset($validated['avatar']);

        $newAvatar = null;

        try {
            if ($avatar) {
                $newAvatar = $fileUploadService->store(
                    $avatar,
                    'images/user-images',
                    'avatar'
                );

                $validated['avatar'] = $newAvatar;
            }

            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => "{$user->name} has been created successfully.",
            ]);

        } catch (\Throwable $exception) {
            $fileUploadService->delete($newAvatar);

            throw $exception;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role:id,name,slug');

        return response()->json([
            'success' => true,
            'html' => view(
                'pages.users.components.user-view',
                compact('user')
            )->render(),
            'message' => 'user loaded'
        ]);
    }
    /**
     * Show the specified user for editing.
     */
    public function edit(User $user)
    {
        $user->load('role:id,name,slug');

        $roles = Role::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'html' => view(
                'pages.users.components.user-edit',
                compact('user', 'roles')
            )->render(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, FileUploadService $fileUploadService)
    {
        $validated = $request->validated();

        $requestedRole = Role::findOrFail($validated['role_id']);

        if ($user->role?->slug === 'super-admin') {
            unset($validated['role_id']);
        }

        if (
            $user->role?->slug !== 'super-admin'
            && $requestedRole->slug === 'super-admin'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'The Super Administrator role cannot be assigned to another user.',
            ], 403);
        }

        $avatar = $validated['avatar'] ?? null;

        unset($validated['avatar']);

        /*
         * Password is only changed when a new password
         * has actually been provided.
         */
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $oldAvatar = $user->avatar;
        $newAvatar = null;

        try {
            if ($avatar) {
                $newAvatar = $fileUploadService->store(
                    $avatar,
                    'images/user-images',
                    'avatar'
                );

                $validated['avatar'] = $newAvatar;
            }

            $user->update($validated);

            if ($newAvatar && $oldAvatar) {
                $fileUploadService->delete($oldAvatar);
            }

            return response()->json([
                'success' => true,
                'message' => "{$user->name} has been updated successfully.",
            ]);

        } catch (\Throwable $exception) {
            if ($newAvatar) {
                $fileUploadService->delete($newAvatar);
            }

            throw $exception;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        if ($user->role?->slug === 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'The Super Administrator account cannot be deleted.',
            ], 403);
        }

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $userName = $user->name;

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "{$userName} has been deleted successfully.",
        ]);
    }
    /**
     * Toggle the user's account status.
     */
    public function toggleStatus(User $user)
    {
        if ($user->role?->slug === 'super-admin') {
            return response()->json([
                'success' => false,
                'message' => 'The Super Administrator account cannot be deactivated.',
            ], 403);
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active
                ? "{$user->name} has been activated successfully."
                : "{$user->name} has been deactivated successfully.",
        ]);
    }
    /**
     * Export users.
     */
    public function export(ExportUsersRequest $request, ExportService $exportService)
    {
        $format = $request->format ?? 'csv';

        $export = new UsersExport($request);

        return $exportService->download(
            export: $export,
            format: $format,
        );
    }


}
