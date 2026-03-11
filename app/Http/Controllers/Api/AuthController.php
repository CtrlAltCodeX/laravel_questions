<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GoogleUser;
use App\Models\UserSession;
use App\Models\UserToken;
use App\Models\Setting;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'email' => 'required|email',
            'name' => 'required',
        ]);

        $email = $request->email;
        $name = $request->name;
        $token = $request->token;

        try {
            $user = GoogleUser::where('email', $email)->first();

            if (!$user) {
                $setting = Setting::first();
                $welcomeCoin = $setting ? $setting->welcome_coin : 0;

                $prefix = !empty($name) ? substr(strtolower($name), 0, 5) : 'user';
                $randomNumber = mt_rand(10000, 99999);
                $referralCode = strtoupper($prefix . $randomNumber);

                $user = GoogleUser::create([
                    'name' => $name,
                    'email' => $email,
                    'login_type' => 'google',
                    'referral_code' => $referralCode,
                    'coins' => $welcomeCoin, // Assuming 'coins' column exists based on login.php line 72
                ]);
            }

            // Generate new session
            $sessionId = bin2hex(random_bytes(16));
            UserSession::create([
                'google_users_id' => $user->id,
                'session_id' => $sessionId,
            ]);

            // Save token if provided
            if ($token) {
                UserToken::updateOrCreate(
                    ['user_id' => $user->id, 'token' => $token],
                    ['token' => $token]
                );
            }

            return response()->json([
                'success' => true,
                'sessionId' => $sessionId,
                'id' => $user->id
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
        ]);

        $sessionId = $request->session_id;

        $deleted = UserSession::where('session_id', $sessionId)->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Session deleted successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting session or session not found.'
            ]);
        }
    }
}
