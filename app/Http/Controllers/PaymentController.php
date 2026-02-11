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
use App\Models\UserCoin;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    private $razorpay_key;
    private $razorpay_secret;

    public function __construct()
    {
        $this->razorpay_key = config('services.razorpay.key');
        $this->razorpay_secret = config('services.razorpay.secret');
    }

    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'user_id'   => 'required|exists:google_users,id',
            'course_id' => 'required|exists:courses,id',
            'plan_type' => 'required|in:monthly,semi_annual,annual',
        ]);

        $amountResponse = $this->getFinalAmount($request);
        $amountData = json_decode($amountResponse->getContent(), true);

        if (!$amountData || !isset($amountData['final_amount'])) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to get final amount'
            ], 400);
        }

        $finalAmountInPaise = $amountData['final_amount'] * 100;

        try {
            $api = new Api($this->razorpay_key, $this->razorpay_secret);

            $callback_url = url('/api/payment/callback') . '?' . http_build_query($validated);

            $paymentLink = $api->paymentLink->create([
                'amount' => $finalAmountInPaise,
                'currency' => 'INR',
                'accept_partial' => false,
                'description' => "Payment for Course: " . ($amountData['course_name'] ?? $validated['course_id']),
                'notify' => [
                    'sms' => true,
                    'email' => true,
                ],
                'callback_url' => $callback_url,
                'callback_method' => 'get'
            ]);

            return response()->json([
                'status' => true,
                'payment_link' => $paymentLink['short_url'],
                'amount' => $amountData['final_amount'],
                'plan' => $validated['plan_type']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        if ($request->has(['razorpay_payment_id', 'user_id', 'course_id', 'plan_type'])) {
            
            $payment_id = $request->razorpay_payment_id;
            
            try {
                $api = new Api($this->razorpay_key, $this->razorpay_secret);
                $payment = $api->payment->fetch($payment_id);

                $data = [
                    'payment_id' => $payment_id,
                    'currency'   => $payment['currency'],
                    'status'     => $payment['status'],
                    'user_id'    => $request->user_id,
                    'course_id'  => $request->course_id,
                    'plan_type'  => $request->plan_type,
                    'method'        => $payment['method'] ?? 'unknown',
                    'card_last4'    => $payment['card']['last4'] ?? null,
                    'card_network'  => $payment['card']['network'] ?? null,
                    'vpa'           => $payment['vpa'] ?? null,
                ];

                // Create a new request for the store method
                $storeRequest = new Request($data);
                $response = $this->store($storeRequest);
                
                $result = json_decode($response->getContent(), true);

                if (isset($result['status']) && $result['status'] == 'success') {
                    return "<h2>Payment Successful!</h2><p>Payment ID: $payment_id</p>";
                } else {
                    return "<h2>Payment Verification Failed</h2><p>" . ($result['message'] ?? 'Unknown error') . "</p>";
                }

            } catch (\Exception $e) {
                return "<h2>Error fetching payment details</h2><p>" . $e->getMessage() . "</p>";
            }
        }

        return "<h2>Invalid Request</h2>";
    }

    public function index()
    {
       
        $payments = Payment::with(['user', 'course'])->get();
        $userCoins = UserCoin::with('user')->get();
        $mergedData = $payments->map(function ($item) {
            return [
                'source' => 'Payment',
                'id' => $item->id,
                'user_name' => $item->user->name ?? '-',
                'email' => $item->email ?? '-',
                'contact' => $item->contact ?? '-',
                'course_name' => $item->course->name ?? '-',
                'amount' => $item->amount ?? '-',
                'currency' => $item->currency ?? '-',
                'payment_id' => $item->payment_id ?? '-',
                'method' => $item->method ?? '-',
                'card_network' => $item->card_network ?? '-',
                'card_last4' => $item->card_last4 ?? '-',
                'vpa' => $item->vpa ?? '-',
                'status' => $item->status ?? '-',
                'created_at' => $item->created_at,
            ];
        });

        $userCoinData = $userCoins->map(function ($item) {
            return [
                'source' => 'User Coin',
                'id' => $item->id,
                'user_name' => $item->user->name ?? '-',
                'email' => $item->user->email ?? '-',
                'contact' => $item->user->phone_number ?? '-',
                'course_name' => '-',
                'amount' => $item->coin ?? '-',
                'currency' => 'INR',
                'payment_id' => $item->meta_description ?? '-',
                'method' => '-',
                'card_network' => '-',
                'card_last4' => '-',
                'vpa' => '-',
                'status' => $item->user->status ?? '-',
                'created_at' => $item->created_at,
            ];
        });
        $merged = $mergedData->merge($userCoinData)->sortByDesc('created_at');
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($currentPage, $perPage),
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('payment-history.index', ['payments' => $paginated]);
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
            'method'     => 'nullable|string',
            'card_last4' => 'nullable|string',
            'card_network' => 'nullable|string',
            'vpa'        => 'nullable|string',
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
