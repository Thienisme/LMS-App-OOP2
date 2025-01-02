<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    private function deleteOldProfileImage($user)
    {
        if ($user->profile_img && Storage::disk('public')->exists('profile/' . $user->profile_img)) {
            Storage::disk('public')->delete('profile/' . $user->profile_img);
        }
    }

    private function saveProfileImage($file, $user)
    {
        $filename = 'profile_' . $user->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('profile', $filename, 'public');
        return $filename;
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        \DB::transaction(function () use ($request, $user, $validatedData) {
            if ($request->hasFile('profile_img')) {
                $this->deleteOldProfileImage($user);
                $filename = $this->saveProfileImage($request->file('profile_img'), $user);
                $validatedData['profile_img'] = $filename;
            }

            $user->fill($validatedData);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        });

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

        // Xóa ảnh profile nếu tồn tại
        $this->deleteOldProfileImage($user);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}