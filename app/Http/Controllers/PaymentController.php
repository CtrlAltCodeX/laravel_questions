<?php
// app/Http/Controllers/Api/PaymentController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Offer;
use App\Models\UserCourse;
use App\Models\GoogleUser;


class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/save-payment",
     *     summary="Store a new payment and update user course subscription",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_id","currency","status","user_id","course_id","plan_type"},
     *             @OA\Property(property="payment_id", type="string", example="pay_123456"),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="course_id", type="integer", example=5),
     *             @OA\Property(property="plan_type", type="string", enum={"monthly","semi_annual","annual"}, example="monthly")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Payment saved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="payment_id", type="string", example="pay_123456"),
     *                 @OA\Property(property="currency", type="string", example="INR"),
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="course_id", type="integer", example=5),
     *                 @OA\Property(property="plan_type", type="string", example="monthly"),
     *                 @OA\Property(property="amount", type="number", example=899.99),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="contact", type="string", example="9876543210"),
     *                 @OA\Property(property="created_at", type="string", example="2025-09-05T10:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid plan type or subscription data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Course not found or User not found"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|unique:payments',
            'currency'   => 'required|string',
            'status'     => 'required|string',
            'user_id'    => 'required|exists:google_users,id',
            'course_id'  => 'required|exists:courses,id',
            'plan_type'  => 'required|in:monthly,semi_annual,annual',
        ]);

        // Get user
        $user = GoogleUser::find($validated['user_id']);

        // Get course
        $course = Course::find($validated['course_id']);
        if (!$course) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found.'
            ], 404);
        }

    
        $courseSubscription = $course->subscription;
        $plan = $validated['plan_type'];

        if (!isset($courseSubscription[$plan]['amount'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid plan type or missing subscription amount.'
            ], 400);
        }

        $amount = floatval($courseSubscription[$plan]['amount']);
        $discount = 0;

    
        $offer = Offer::whereJsonContains('course', (string) $course->id)
            ->latest('created_at')
            ->first();

        $offerSubscription = $offer ? json_decode($offer->subscription, true) : [];

        $alreadyPurchased = UserCourse::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->exists();

        if ($offer && isset($offerSubscription[$plan])) {
            if ($alreadyPurchased) {
                $discount = isset($offerSubscription[$plan]['upgrade']) ? floatval($offerSubscription[$plan]['upgrade']) : 0;
            } else {
                $discount = isset($offerSubscription[$plan]['discount']) ? floatval($offerSubscription[$plan]['discount']) : 0;
            }
        }

        $finalAmount = $amount - (($discount / 100) * $amount);
        $finalAmount = round($finalAmount, 2);

        $validated['email'] = $user->email;
        $validated['contact'] = $user->phone_number;
        $validated['amount'] = $finalAmount;

        // Save payment
        $payment = Payment::create($validated);

        // Handle UserCourse validity
        $validFrom = now();
        $validityDays = isset($courseSubscription[$plan]['validity']) ? intval($courseSubscription[$plan]['validity']) : 0;
        $validTo = $validFrom->copy()->addDays($validityDays);

        // Create or update UserCourse
        \App\Models\UserCourse::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'course_id' => $validated['course_id'],
            ],
            [
                'subscription_type' => $plan,
                'valid_from' => $validFrom,
                'valid_to' => $validTo,
            ]
        );


        return response()->json([
            'status' => 'success',
            'message' => 'Payment saved successfully',
            'data' => $payment
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/get-final-amount",
     *     summary="Get final payable amount for a course considering offers and subscription plan",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","course_id","plan_type"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="course_id", type="integer", example=5),
     *             @OA\Property(property="plan_type", type="string", enum={"monthly","semi_annual","annual"}, example="annual")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Final amount calculated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="final_amount", type="number", example=750.00),
     *             @OA\Property(property="original_amount", type="number", example=1000.00),
     *             @OA\Property(property="discount_percentage", type="number", example=25),
     *             @OA\Property(property="plan_type", type="string", example="annual"),
     *             @OA\Property(property="course_name", type="string", example="Advanced Laravel Mastery")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid plan type or missing subscription data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User or Course not found"
     *     )
     * )
     */
    public function getFinalAmount(Request $request)
    {
        $validated = $request->validate([
            'user_id'   => 'required|exists:google_users,id',
            'course_id' => 'required|exists:courses,id',
            'plan_type' => 'required|in:monthly,semi_annual,annual',
        ]);

        $user = GoogleUser::find($validated['user_id']);
        $course = Course::find($validated['course_id']);
        $plan = $validated['plan_type'];
        $courseSubscription = $course->subscription;

        if (!isset($courseSubscription[$plan]['amount'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid plan type or missing subscription data.'
            ], 400);
        }

        $amount = floatval($courseSubscription[$plan]['amount']);
        $discount = 0;

        // Find offer
        $offer = Offer::whereJsonContains('course', (string) $course->id)
            ->latest('created_at')
            ->first();

        $offerSubscription = $offer ? json_decode($offer->subscription, true) : [];

        // Check if course already purchased
        $alreadyPurchased = UserCourse::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->exists();

        if ($offer && isset($offerSubscription[$plan])) {
            if ($alreadyPurchased) {
                $discount = isset($offerSubscription[$plan]['upgrade']) ? floatval($offerSubscription[$plan]['upgrade']) : 0;
            } else {
                $discount = isset($offerSubscription[$plan]['discount']) ? floatval($offerSubscription[$plan]['discount']) : 0;
            }
        }

        $finalAmount = $amount - (($discount / 100) * $amount);
        $finalAmount = round($finalAmount, 2);

        return response()->json([
            'status' => true,
            'final_amount' => $finalAmount,
            'original_amount' => $amount,
            'discount_percentage' => $discount,
            'plan_type' => $plan,
            'course_name' => $course->name,
        ]);
    }
}
