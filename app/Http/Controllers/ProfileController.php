<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy($id, Request $request): RedirectResponse
    {
        // $request->validateWithBag('userDeletion', [
        //     'password' => ['required', 'current_password'],
        // ]);
        // Log::info('Delete request received for user ID: ' . $id);
        $user=User::findOrFail($id);
        if(auth()->user()->id === $user->id){
            return redirect()->route('users.index')->with('error','You cannot delete your own account');
        }
        // $user = $request->user();

        // Auth::logout();

        $user->delete();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // return Redirect::to('/');
        return redirect()->route('users.index')->with('success','User deleted successfully!');
    }
    // public function destroyUser(Request $request):RedirectResponse
    // {
    //     $user->delete();
    //     return redirect()->route('users.index')->with('success','User deleted successfully!');

    // }

    public function users()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }
}
