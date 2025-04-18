<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoogleUser;

/**
 * @OA\Schema(
 *     schema="GoogleUser",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="login_type", type="string"),
 *     @OA\Property(property="referral_code", type="string"),
 *     @OA\Property(property="friend_code", type="string"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="login_date", type="string", format="date"),
 *     @OA\Property(property="profile_image", type="string"),
 *     @OA\Property(property="language_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class GoogleUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/users/{id}/update",
     *     summary="Update user details",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="phone_number", type="string"),
     *                 @OA\Property(property="login_type", type="string", enum={"google", "facebook", "apple"}),
     *                 @OA\Property(property="referral_code", type="string"),
     *                 @OA\Property(property="friend_code", type="string"),
     *                 @OA\Property(property="status", type="string", enum={"Enabled", "Disabled"}),
     *                 @OA\Property(property="login_date", type="string", format="date"),
     *                 @OA\Property(property="profile_image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/GoogleUser")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
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
            $filename = time() . '_' . $file->getClientOriginalName();
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

    /**
     * @OA\Post(
     *     path="/api/users/{id}/update-language-category",
     *     summary="Update user's language and category",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="language_id", type="integer"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Language and Category updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/GoogleUser")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/users/{id}/profile",
     *     summary="Get user profile",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/GoogleUser")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
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
