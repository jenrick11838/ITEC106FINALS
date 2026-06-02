<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $notes = $user->notes()->latest()->take(5)->get();

        return view('profile.index', compact('user', 'notes'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'gender'  => ['nullable', 'in:male,female,other,prefer_not_to_say'],
            'bio'     => ['nullable', 'string', 'max:1000'],
        ]);

        // Password change (optional)
        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => ['required'],
                'new_password'     => ['required', 'min:8', 'confirmed'],
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->gender  = $request->gender;
        $user->bio     = $request->bio;
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old avatar
        if ($user->avatar) {
            $oldPath = storage_path("app/public/avatars/{$user->avatar}");
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $filename = uniqid('avatar_') . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->storeAs('avatars', $filename, 'public');

        $user->avatar = $filename;
        $user->save();

        return back()->with('success', 'Profile picture updated.');
    }
}