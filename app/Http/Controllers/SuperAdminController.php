<?php

namespace App\Http\Controllers;


// use App\Models\User;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;


class SuperAdminController extends Controller
{

    public function super_admin()
    {
        $users = User::where('role', 'Super admin')->get();
        return view('super-admin.index', compact('users'));
    }

    public function show()
    {

        return view('super-admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'Super admin',
        ]);

        return redirect()->route('super-admin.index')->with('success', 'Super admin created successfully!');
    }

    public function edit(Request $request, $id): View
    {
        $userToEdit = User::findOrFail($id);

        $currentUser = Auth::user();

        if ($currentUser->isSuperAdmin()) {
            return view('super-admin.edit', [
                'user' => $userToEdit,
            ]);
        }
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, $id): RedirectResponse
    {

        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->role == 'Super admin') {
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
                'role' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:6',
            ]);

            $filteredData = array_filter($validatedData, function ($value) {
                return !is_null($value) && $value !== '';
            });
            $user->update($filteredData);
        }

        return redirect()->route('super-admin.index')
            ->with('success', 'Admin profile updated successfully.');
    }
}
