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

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;

class PaymentController extends Controller
{

    public function index()
    {
        $payments = Payment::with(['user', 'course'])
            ->latest()
            ->paginate(10);
        return view('payment-history.index', compact('payments'));
    }


    public function exportExcel()
    {
        return Excel::download(new PaymentsExport, 'payment-history.xlsx');
    }

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
