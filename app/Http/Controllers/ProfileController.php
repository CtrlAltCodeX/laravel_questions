<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\GoogleUser;
use App\Models\User;
use App\Models\UserCoin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request, $id): View
    {
        $userToEdit = User::findOrFail($id);

        $currentUser = Auth::user();
        
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

        if($currentUser->role == 'Super admin'){
            $validatedData= $request->validate([
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
       
        return redirect()->route('users.index')
                         ->with('success', 'Admin profile updated successfully.');

        
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

  public function users(Request $request)
{
    $query = GoogleUser::with([
        'category.language',
        'userCourses.course' ,
         'coinsHistory'
    ]);

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $users = $query->paginate(10);

    return view('users.index', compact('users'));
}


public function deleteUser($id)
{
    try {
        // User find karein
        $user = GoogleUser::findOrFail($id);

    
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
    }
}


public function updateCoinsAndStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required',
        'coins' => 'nullable|integer',
        'meta_data' => 'nullable|string',
    ]);

    $user = GoogleUser::findOrFail($id);

    // ✅ Update status in GoogleUser
    $user->status = $request->status;
    $user->save();

    // ✅ Add coin entry in new table
    if ($request->coins > 0) {
        UserCoin::create([
            'user_id' => $user->id,
            'coin' => $request->coins,
            'meta_description' => $request->meta_data,
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Coins added and status updated successfully.',
        'user' => $user
    ], 201);
}
}
