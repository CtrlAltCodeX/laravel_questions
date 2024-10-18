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
    // public function edit(Request $request): View
    // {
    //     return view('profile.edit', [
    //         'user' => $request->user(),
    //     ]);
    // }
     public function edit(Request $request, $id): View
    {
        $userToEdit=User::findOrFail($id);

        $currentUser= Auth::user();
        
        if($currentUser->isSuperAdmin()){
            return view('users.edit', [
                'user' => $userToEdit, 
            ]);
        }
        
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, $id): RedirectResponse
    {
        // $request->user()->fill($request->validated());

        // if ($request->user()->isDirty('email')) {
        //     $request->user()->email_verified_at = null;
        // }

        $user=User::findOrFail($id);
        $currentUser=Auth::user();

        // dd($request->all());

        if($currentUser->role == 'Super admin'){
            $validatedData= $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
                'role' => 'nullable|string|max:255',
            ]);

            // $user->update([
            //     'name' => $request->input('name'),
            //     'email' => $request->input('email'),
            //     'role' => $request->input('role'),
            // ]);
            // $data=$request->only(['name','email','role']);
            $filteredData = array_filter($validatedData, function ($value) {
                return !is_null($value) && $value !== '';
            });
            // dd($data);
            $user->update($filteredData);
        }
       
        return redirect()->route('users.index')
                         ->with('success', 'Admin profile updated successfully.');

        // $request->user()->save();

        // return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
        // $users = User::all();
        $users = User::where('role','Admin')->get();
        return view('users.index', compact('users'));
    }

    public function super_admin()
    {
        $users = User::where('role','Super admin')->get();
        return view('super-admin.index', compact('users'));
    }
}
