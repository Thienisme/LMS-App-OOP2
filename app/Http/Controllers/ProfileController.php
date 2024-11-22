<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Get validated data
        $validated = $request->validated();
        
        // Handle profile image upload
        if ($request->hasFile('profile_img')) {
            // Delete old profile image if exists
            if ($request->user()->profile_img) {
                $oldPath = public_path('profile/') . $request->user()->profile_img;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Store new profile image
            $file = $request->file('profile_img');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('profile'), $filename);
            
            // Update profile_img in validated data
            $validated['profile_img'] = $filename;
        }

        // Fill user data
        $request->user()->fill($validated);

        // Check if email was changed
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile image if exists
        if ($user->profile_img) {
            $imagePath = public_path('profile/') . $user->profile_img;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}