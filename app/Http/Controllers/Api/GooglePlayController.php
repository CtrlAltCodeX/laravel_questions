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
    private function getAndroidPublisherService(): AndroidPublisher
    {
        $client = new Client();
        $keyPath = base_path(config('services.google.service_account_json'));
        $client->setAuthConfig($keyPath);
        $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);
        return new AndroidPublisher($client);
    }

    private function resolveUser(Request $request): ?GoogleUser
    {
        if ($request->has('user_id')) {
            return GoogleUser::find($request->user_id);
        }
        if ($request->has('email')) {
            return GoogleUser::where('email', $request->email)->first();
        }
        return null;
    }

    public function lookupByEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = GoogleUser::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['user_id' => $user->id]
        ]);
    }

    public function verifySubscription(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'purchase_token' => 'required|string',
            'subscription_id' => 'required|string',
            'plan_type' => 'required|in:monthly,semi_annual,annual',
            'email' => 'required_without:user_id|email',
            'user_id' => 'required_without:email|integer',
        ]);

        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {
            $service = $this->getAndroidPublisherService();
            $packageName = config('app.package_name');

            $subscription = $service->purchases_subscriptionsv2->get(
                $packageName,
                $request->purchase_token
            );

            $subscriptionState = $subscription->getSubscriptionState();
            $lineItems = $subscription->getLineItems();

            if (empty($lineItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subscription line items found'
                ], 402);
            }

            $lineItem = $lineItems[0];
            $expiryTime = $lineItem->getExpiryTime();
            $productId = $lineItem->getProductId();
            $autoRenewingPlan = $lineItem->getAutoRenewingPlan();
            $autoRenewing = $autoRenewingPlan ? $autoRenewingPlan->getAutoRenewEnabled() : false;

            $validTo = date('Y-m-d H:i:s', strtotime($expiryTime));

            $activeStates = [
                'SUBSCRIPTION_STATE_ACTIVE',
                'SUBSCRIPTION_STATE_IN_GRACE_PERIOD',
            ];

            if (!in_array($subscriptionState, $activeStates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription is not active',
                    'subscription_state' => $subscriptionState
                ], 402);
            }

            $existing = UserCourse::where('user_id', $user->id)
                ->where('course_id', $request->course_id)
                ->first();

            $metaPayload = json_encode([
                'provider' => 'google_play',
                'purchase_token' => $request->purchase_token,
                'subscription_id' => $request->subscription_id,
                'product_id' => $productId,
                'auto_renewing' => $autoRenewing,
                'order_id' => $subscription->getLatestOrderId(),
            ]);

            if ($existing) {
                $existingMeta = json_decode($existing->meta_data, true);
                $existingProvider = $existingMeta['provider'] ?? 'razorpay';
                $existingActive = $existing->valid_to && strtotime($existing->valid_to) > time();

                if ($existingActive && !str_contains($existingProvider, 'google_play')) {
                    $userCourse = UserCourse::create([
                        'user_id' => $user->id,
                        'course_id' => $request->course_id,
                        'subscription_type' => $request->plan_type,
                        'valid_from' => now(),
                        'valid_to' => $validTo,
                        'status' => 1,
                        'meta_data' => $metaPayload,
                    ]);
                } else {
                    $existing->update([
                        'subscription_type' => $request->plan_type,
                        'valid_from' => now(),
                        'valid_to' => $validTo,
                        'status' => 1,
                        'meta_data' => $metaPayload,
                    ]);
                    $userCourse = $existing;
                }
            } else {
                $userCourse = UserCourse::create([
                    'user_id' => $user->id,
                    'course_id' => $request->course_id,
                    'subscription_type' => $request->plan_type,
                    'valid_from' => now(),
                    'valid_to' => $validTo,
                    'status' => 1,
                    'meta_data' => $metaPayload,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription verified successfully',
                'data' => [
                    'user_id' => $user->id,
                    'course_id' => $request->course_id,
                    'plan_type' => $request->plan_type,
                    'subscription_source' => 'google_play',
                    'valid_from' => $userCourse->valid_from,
                    'valid_to' => $validTo,
                    'auto_renewing' => $autoRenewing,
                    'order_id' => $subscription->getLatestOrderId(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPurchase(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'purchase_token' => 'required|string',
            'product_id' => 'required|string',
            'plan_type' => 'required|string',
            'email' => 'required_without:user_id|email',
            'user_id' => 'required_without:email|integer',
        ]);

        $user = $this->resolveUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {
            $service = $this->getAndroidPublisherService();
            $packageName = config('app.package_name');

            $purchase = $service->purchases_products->get(
                $packageName,
                $request->product_id,
                $request->purchase_token
            );

            if ($purchase->getPurchaseState() !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid purchase state'
                ], 402);
            }

            $course = Course::find($request->course_id);
            $courseSubscription = $course->subscription;
            $plan = $request->plan_type;

            $validityDays = isset($courseSubscription[$plan]['validity']) ? intval($courseSubscription[$plan]['validity']) : 30;
            $validTo = now()->addDays($validityDays);

            $userCourse = UserCourse::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $request->course_id,
                ],
                [
                    'subscription_type' => $plan,
                    'valid_from' => now(),
                    'valid_to' => $validTo,
                    'status' => 1,
                    'meta_data' => json_encode([
                        'provider' => 'google_play_product',
                        'purchase_token' => $request->purchase_token,
                        'product_id' => $request->product_id,
                    ])
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Purchase verified successfully',
                'data' => [
                    'user_id' => $user->id,
                    'course_id' => $request->course_id,
                    'plan_type' => $plan,
                    'subscription_source' => 'google_play',
                    'valid_to' => $validTo->toDateTimeString(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
