<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GoogleUser;
use App\Models\UserSession;
use App\Models\UserFcmToken;
use App\Models\Setting;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'id_token' => 'required', // Google ID Token from Mobile App
        ]);

        try {
            $idToken = $request->id_token;
            $client = new \Google\Client(['client_id' => config('services.google.client_id')]); 
            
            $payload = $client->verifyIdToken($idToken);
            
            if ($payload) {
                $email = $payload['email'];
                $name = $payload['name'] ?? 'Google User';
                $googleId = $payload['sub']; // Unique Google ID
                $token = $request->token; // FCM Token
                
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
                        'coins' => $welcomeCoin,
                    ]);
                }

                // Generate new session
                $sessionId = bin2hex(random_bytes(16));
                UserSession::create([
                    'google_users_id' => $user->id,
                    'session_id' => $sessionId,
                ]);

                // Save FCM token if provided
                if ($token) {
                    UserFcmToken::updateOrCreate(
                        ['user_id' => $user->id],
                        ['fcm_token' => $token]
                    );
                }

                return response()->json([
                    'success' => true,
                    'sessionId' => $sessionId,
                    'id' => $user->id
                ]);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID Token'
                ], 401);
            }

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
