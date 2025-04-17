<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoogleUser;
use Illuminate\Support\Facades\Storage;

class GoogleUserController extends Controller
{
    // 1. Update User Info + Image Upload
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name'           => 'nullable|string',
            'email'          => 'nullable|email',
            'phone_number'   => 'nullable|string',
            'login_type'     => 'nullable|in:google,facebook,apple',
            'referral_code'  => 'nullable|string',
            'friend_code'    => 'nullable|string',
            'status'         => 'nullable|in:Enabled,Disabled',
            'login_date'     => 'nullable|date',
            'profile_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        $user = GoogleUser::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/profile_images', $filename);
            $user->profile_image = 'profile_images/' . $filename;
        }
    
        $user->update($request->except(['profile_image']));
    
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
    
    // 2. Update language_id & category_id
    public function updateLanguageCategory(Request $request, $id)
    {
        $request->validate([
            'language_id' => 'nullable|integer',
            'category_id' => 'nullable|integer'
        ]);
    
        $user = GoogleUser::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    
        $user->language_id = $request->language_id ?? $user->language_id;
        $user->category_id = $request->category_id ?? $user->category_id;
        $user->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Language and Category updated',
            'data' => $user
        ]);
    }
    

    // 3. Delete User
    public function deleteUser($id)
    {
        $user = GoogleUser::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    
        $user->delete();
    
        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    

    // 4. Get User Profile
    public function getProfile($id)
    {
        $user = GoogleUser::find($id);
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Profile fetched',
            'data' => $user
        ]);
    }
    
}
