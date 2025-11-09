<?php

namespace App\Http\Controllers;

use App\Mail\OTP;
use Illuminate\Http\Request;
use App\Models\GoogleUser;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

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
     * @OA\Put(
     *     path="/api/user/{id}/update",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Update user information",
     *     description="Updates the specified user's details including name, phone number, profile image, and preferences.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={},
     *                 @OA\Property(property="phone_number", type="string", example="9876543210"),
     *                 @OA\Property(property="login_type", type="string", enum={"google", "facebook", "apple"}, example="google"),
     *                 @OA\Property(property="friend_code", type="string", example="FRIEND123"),
     *                 @OA\Property(property="profile_image", type="string", format="binary"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="language_id", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"profile_image": {"The profile image must be an image."}}
     *             )
     *         )
     *     )
     * )
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name'           => 'nullable|string',
            'phone_number'   => 'nullable|string',
            'login_type'     => 'nullable|in:google,facebook,apple',
            'friend_code'    => 'nullable|string',
            'profile_image'  => 'nullable',
            'category_id'    => 'nullable|integer|exists:categories,id',
            'language_id'    => 'nullable|integer|exists:languages,id'
        ]);

        $user = GoogleUser::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->except('profile_image');

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/profile_images', $filename);
            $data['profile_image'] = 'profile_images/' . $filename;
        } elseif ($request->filled('profile_image')) {
            $data['profile_image'] = $request->profile_image;
        }

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function generateOTP($n)
    {
        $generator = "1357902468";
        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, rand() % strlen($generator), 1);
        }

        // Returning the result
        return $result;
    }

    /**
     * @OA\Delete(
     *     path="/api/{id}/user",
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

        if (!request()->otp) {
            $otp = $this->generateOTP(6);

            Mail::to($user->email)
                ->send(new OTP([
                    'otp' => __($otp),
                ]));

            $user->update([
                'otp' => $otp
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OTP successfully sent'
            ], 200);
        } else {
            $originalOTP = $user->otp;

            if ($originalOTP != request()->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP'
                ], 404);
            }
        }

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
     *     path="/api/user/{id}/profile",
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
            'message' => 'User profile retrieved successfully',
            'data' => $user
        ]);
    }

    public function updateUserCode(Request $request, $id)
    {
        $request->validate([
            'friend_code'   => 'nullable|string',
        ]);

        $user = GoogleUser::where('referral_code', $request->friend_code)
            ->first();

        $settings = Setting::first();

        $currentUser = GoogleUser::find($id);

        if (!$currentUser) {
            return response()->json([
                'status' => false,
                'message' => 'No User found'
            ], 404);
        }

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No Referral code not found'
            ], 404);
        }

        $coin = $user->coins += $settings->refer_coin;

        $user->update([
            'friend_code' => $currentUser->referral_code,
            'coins' => $coin
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Friend code updated successfully',
            'data' => $user
        ]);
    }
}
