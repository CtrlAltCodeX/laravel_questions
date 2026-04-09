<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleUser;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\Payment;
use Google\Client;
use Google\Service\AndroidPublisher;
use Exception;

use App\Services\NotificationService;
use Google\Service\AndroidPublisher\SubscriptionPurchase;

class GooglePlayController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Verify Google Play Subscription
     */
    public function verifySubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:google_users,id',
            'course_id' => 'required|exists:courses,id',
            'subscription_id' => 'required|string',
            'purchase_token' => 'required|string',
            'plan_type' => 'required|in:monthly,semi_annual,annual'
        ]);

        // Duplicate Protection (Using Payment table for exact match)
        $exists = Payment::where('payment_id', $request->purchase_token)->exists();
        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription already processed'
            ]);
        }

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
            // Check Payment State (1 = Payment received)
            if ($subscription->getPaymentState() !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription payment not completed (State: ' . $subscription->getPaymentState() . ')'
                ], 402);
            }

            // Expiry Check
            if (time() > $expiryTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription has expired'
                ], 402);
            }

            $validTo = \Carbon\Carbon::createFromTimestamp($expiryTime);

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


            // Fetch User for email/contact

            $user = GoogleUser::find($request->user_id);

            // Extract Amount (Micros to Main Unit)
            $amountInMicros = $subscription->getPriceAmountMicros();
            $amount = $amountInMicros ? ($amountInMicros / 1000000) : 0;

            // Save Payment record (UpdateOrCreate for safety)
            Payment::updateOrCreate(
            ['payment_id' => $request->purchase_token],
            [
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'amount' => $amount,
                'currency' => $subscription->getPriceCurrencyCode() ?? 'INR',
                'status' => 'success',
                'method' => 'google_play_subscription',
                'email' => $user->email ?? null,
                'contact' => $user->phone_number ?? null,
            ]
            );

            // Send Notification
            $this->notificationService->send([
                'user_id' => $request->user_id,
                'title' => 'Payment Successful',
                'message' => 'Your subscription has been verified successfully.',
                'source' => 'google_play',
                'type' => 'payment',
                'action_url' => '/Payments'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription verified successfully',
                'valid_to' => $validTo
            ]);

        }
        catch (Exception $e) {
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
            'product_id' => 'required|string',
            'purchase_token' => 'required|string',
        ]);

        // Duplicate Protection
        $exists = Payment::where('payment_id', $request->purchase_token)->exists();
        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase already processed'
            ]);
        }

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
            $plan = $request->plan_type ?? 'lifetime';

            $validityDays = isset($courseSubscription[$plan]['validity']) ? intval($courseSubscription[$plan]['validity']) : 365;
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

            // Fetch User
            $user = GoogleUser::find($request->user_id);

            // Extract Amount (ProductPurchase might not have amount in some versions)
            $amount = 0;

            // Save Payment record
            Payment::updateOrCreate(
            ['payment_id' => $request->purchase_token],
            [
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'success',
                'method' => 'google_play_product',
                'email' => $user->email ?? null,
                'contact' => $user->phone_number ?? null,
            ]
            );

            // Send Notification
            $this->notificationService->send([
                'user_id' => $request->user_id,
                'title' => 'Payment Successful',
                'message' => 'Your purchase has been verified successfully.',
                'source' => 'google_play',
                'type' => 'payment',
                'action_url' => '/Payments'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase verified successfully',
                'valid_to' => $validTo->toDateTimeString()
            ]);

        }
        catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Google Play Real-Time Developer Notification (RTDN)
     */
    public function handleRTDN(Request $request)
    {
        // Google Play sends a Pub/Sub message
        $data = $request->input('message.data');
        if (!$data) {
            return response()->json(['status' => 'error', 'message' => 'No data'], 400);
        }

        $decodedData = json_decode(base64_decode($data), true);
        $packageName = $decodedData['packageName'] ?? null;
        $configuredPackage = config('app.package_name');

        if (!$configuredPackage) {
            \Log::error('RTDN Error: Package name not configured in config/app.php');
            return response()->json(['status' => 'error', 'message' => 'Server configuration error'], 500);
        }

        if ($packageName !== $configuredPackage) {
            return response()->json(['status' => 'error', 'message' => 'Invalid package name'], 400);
        }

        if (isset($decodedData['subscriptionNotification'])) {
            $subNote = $decodedData['subscriptionNotification'];
            $purchaseToken = $subNote['purchaseToken'];
            $notificationType = $subNote['notificationType'];

            $userCourse = UserCourse::with('course')->where('meta_data', 'like', '%' . $purchaseToken . '%')->first();

            if ($userCourse) {
                switch ($notificationType) {
                    case 1: // Recovered
                    case 2: // Renewed
                        $this->notificationService->send([
                            'user_id' => $userCourse->user_id,
                            'title' => 'Subscription Renewed',
                            'message' => 'Your subscription for ' . ($userCourse->course->name ?? 'Course') . ' has been renewed successfully.',
                            'source' => 'google_play',
                            'type' => 'payment',
                            'action_url' => '/Payments'
                        ]);
                        break;
                    case 3: // Canceled
                        // Log cancellation or update meta_data if desired
                        \Log::info('Google Play Subscription Canceled', ['user_id' => $userCourse->user_id]);
                        break;
                    case 12: // Expired
                        $userCourse->update([
                            'valid_to' => now()
                        ]);
                        \Log::info('Google Play Subscription Expired', ['user_id' => $userCourse->user_id]);
                        break;
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}