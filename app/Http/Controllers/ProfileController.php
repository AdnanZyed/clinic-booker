<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = auth()->user()->load('doctor');
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validatedUser = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validatedUser);

        if ($user->type === 'doctor' && $user->doctor) {
            $validatedDoctor = $request->validate([
                'specialization'    => ['required', 'string', 'max:255'],
                'qualifications'    => ['required', 'string', 'max:255'],
                'session_duration'  => ['required', 'integer', 'min:1'],
                'available_days'    => ['required', 'array'],
                'available_days.*'  => ['in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
            ]);

            $user->doctor->update([
                'specialization'    => $validatedDoctor['specialization'] ?? null,
                'qualifications'    => $validatedDoctor['qualifications'] ?? null,
                'session_duration'  => $validatedDoctor['session_duration'] ?? null,
                'available_days'    => isset($validatedDoctor['available_days']) ? json_encode($validatedDoctor['available_days']) : null,
            ]);
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('The current password is incorrect.')]);
        }

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required' => __('The current password is required.'),
            'password.required' => __('The new password is required.'),
            'password.confirmed' => __('The password confirmation does not match.'),
            'password.min' => __('The new password must be at least 8 characters.'),
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('status', 'password-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('The current password is incorrect.')]);
        }

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
