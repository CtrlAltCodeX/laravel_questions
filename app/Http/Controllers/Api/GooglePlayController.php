<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleUser;
use App\Models\Course;
use App\Models\UserCourse;
use Google\Client;
use Google\Service\AndroidPublisher;
use Exception;

class GooglePlayController extends Controller
{
    /**
     * Verify Google Play Subscription
     */
    public function verifySubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:google_users,id',
            'course_id' => 'required|exists:courses,id',
            'purchase_token' => 'required',
            'subscription_id' => 'required', // The ID of the subscription (e.g., 'course_monthly')
            'plan_type' => 'required|in:monthly,semi_annual,annual'
        ]);

        try {
            $client = new Client();
            $client->setAuthConfig(config('services.google.service_account_json'));
            $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

            $service = new AndroidPublisher($client);
            $packageName = config('app.package_name', 'com.example.app'); // Add this to your .env

            $subscription = $service->purchases_subscriptions->get(
                $packageName,
                $request->subscription_id,
                $request->purchase_token
            );

            // Google returns subscription status
            // expiryTimeMillis is the important part
            $expiryTime = $subscription->getExpiryTimeMillis() / 1000;
            $validTo = date('Y-m-d H:i:s', $expiryTime);

            if (time() > $expiryTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription has expired'
                ], 402);
            }

            // Update user's course validity
            UserCourse::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'course_id' => $request->course_id,
                ],
                [
                    'subscription_type' => $request->plan_type,
                    'valid_from' => now(),
                    'valid_to' => $validTo,
                    'meta_data' => json_encode([
                        'provider' => 'google_play',
                        'purchase_token' => $request->purchase_token,
                        'subscription_id' => $request->subscription_id
                    ])
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription verified successfully',
                'valid_to' => $validTo
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Google Play One-Time Purchase (Non-Subscription)
     */
    public function verifyPurchase(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:google_users,id',
            'course_id' => 'required|exists:courses,id',
            'purchase_token' => 'required',
            'product_id' => 'required',
            'plan_type' => 'required'
        ]);

        try {
            $client = new Client();
            $client->setAuthConfig(config('services.google.service_account_json'));
            $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

            $service = new AndroidPublisher($client);
            $packageName = config('app.package_name', 'com.example.app');

            $purchase = $service->purchases_products->get(
                $packageName,
                $request->product_id,
                $request->purchase_token
            );

            // Check if purchaseState is 0 (Purchased)
            if ($purchase->getPurchaseState() !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid purchase state'
                ], 402);
            }

            // For one-time purchases, validity depends on your course business logic
            // Assuming we use the validity from the course model
            $course = Course::find($request->course_id);
            $courseSubscription = $course->subscription;
            $plan = $request->plan_type;
            
            $validityDays = isset($courseSubscription[$plan]['validity']) ? intval($courseSubscription[$plan]['validity']) : 30;
            $validTo = now()->addDays($validityDays);

            UserCourse::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'course_id' => $request->course_id,
                ],
                [
                    'subscription_type' => $plan,
                    'valid_from' => now(),
                    'valid_to' => $validTo,
                    'meta_data' => json_encode([
                        'provider' => 'google_play_product',
                        'purchase_token' => $request->purchase_token,
                        'product_id' => $request->product_id
                    ])
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Purchase verified successfully',
                'valid_to' => $validTo->toDateTimeString()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
