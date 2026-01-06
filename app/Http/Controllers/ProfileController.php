<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        // For now, we'll just pass static data or the authenticated user if available
        // Assuming no strict auth yet based on previous files, but we can pass dummy data matches the standard
        return view('pages.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'emailAddress' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'username' => 'nullable|string|max:255|unique:users,username,' . auth()->id(),
            'phoneNumber' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:6144', // 6MB Max
        ]);

        $user = auth()->user();

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
        }

        $user->name = $request->fullName;
        $user->email = $request->emailAddress;
        $user->username = $request->username;
        $user->phone = $request->phoneNumber;
        $user->bio = $request->bio;
        
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Alamat berhasil diperbarui!');
    }
}
