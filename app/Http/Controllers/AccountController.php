<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\FileUploadService;

class AccountController extends Controller
{
    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {

        $user = $request->user();


        //logout other device except current 
        Auth::logoutOtherDevices($request->current_password);

        $user->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your password has been updated successfully.',
        ]);
    }


    /**
     * Display the authenticated user's profile.
     */
    public function profile()
    {
        return view('pages.account.profile');
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(UpdateProfileRequest $request, FileUploadService $fileUploadService): JsonResponse
    {
        $user = $request->user();

        $oldAvatar = $user->avatar;
        $newAvatar = null;

        try {

            if ($request->hasFile('avatar')) {
                $newAvatar = $fileUploadService->store(
                    $request->file('avatar'),
                    'images/user-images',
                    'avatar'
                );
            }

            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'avatar' => $newAvatar ?? $oldAvatar,
            ]);

            if ($newAvatar) {
                $fileUploadService->delete($oldAvatar);
            }

            return response()->json([
                'success' => true,
                'message' => 'Your profile has been updated successfully.',
            ]);

        } catch (\Throwable $exception) {

            $fileUploadService->delete($newAvatar);

            throw $exception;
        }
    }


}