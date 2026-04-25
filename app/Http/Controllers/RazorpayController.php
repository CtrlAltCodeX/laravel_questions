<?php
// app/Http/Controllers/Api/RazorpayController.php

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

use App\Services\NotificationService;

class RazorpayController extends Controller
{
    private $razorpay_key;
    private $razorpay_secret;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->razorpay_key = config('services.razorpay.key');
        $this->razorpay_secret = config('services.razorpay.secret');
        $this->notificationService = $notificationService;
    }

    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:google_users,id',
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

            $callback_url = url('/api/Razorpay/callback') . '?' . http_build_query($validated);

            $paymentLink = $api->paymentLink->create([
                'amount' => $finalAmountInPaise,
                'currency' => 'INR',
                'accept_partial' => false,
                'description' => "Payment for Course: " . ($amountData['course_name'] ?? $validated['course_id']),
                'notify' => [
                    'sms' => true,
                    'email' => true,
                ],
                'notes' => [
                    'user_id' => $validated['user_id'],
                    'course_id' => $validated['course_id'],
                    'plan_type' => $validated['plan_type']
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

                // $data = [
                //     'payment_id' => $payment_id,
                //     'currency' => $payment['currency'],
                //     'status' => $payment['status'],
                //     'user_id' => $request->user_id,
                //     'course_id' => $request->course_id,
                //     'plan_type' => $request->plan_type,
                //     'method' => $payment['method'] ?? 'unknown',
                //     'card_last4' => $payment['card']['last4'] ?? null,
                //     'card_network' => $payment['card']['network'] ?? null,
                //     'vpa' => $payment['vpa'] ?? null,
                // ];

                // Removed store() call from Callback as per Best Practice (Webhook is real processor)
                // $storeRequest = new Request($data);
                // $this->store($storeRequest);

                $result = ['status' => 'success']; // Mock for the redirect UI

                if (isset($result['status']) && $result['status'] == 'success') {
                    return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(-45deg, #e0f7fa, #a7ffeb, #f1f8e9, #b2dfdb);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: visible;
        }

        .star {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #ffffffcc;
            border-radius: 50%;
            animation: fall linear infinite;
            opacity: 1;
        }

        @keyframes fall {
            0% {
                transform: translateY(-20px);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh);
                opacity: 0;
            }
        }

        .card {
            background: #fff;
            padding: 30px 20px;
            border-radius: 15px;
            box-shadow: 0 0 12px #28a745;
            border: 2px solid #28a745;
            text-align: center;
            width: 70%;
            max-width: 360px;
            animation: glow 1.5s ease-in-out infinite alternate;
            z-index: 1;
        }

        .check-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px auto;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 0 10px #4caf50;
        }

        .check-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card h2 {
            color: #28a745;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .tagline-en, .tagline-hi {
            font-size: 15px;
            color: #222;
            margin-bottom: 5px;
        }

        .countdown {
            font-size: 34px;
            font-weight: bold;
            color: #ff5722;
            margin: 20px 0 10px;
        }

        .zoomed {
            animation: zoom 0.4s ease-in-out;
        }

        @keyframes glow {
            from { box-shadow: 0 0 10px #28a745; }
            to   { box-shadow: 0 0 20px #66bb6a; }
        }

        @keyframes zoom {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.25); }
            100% { transform: scale(1); }
        }

        p {
            font-size: 14px;
            color: #555;
            margin: 0;
        }
    </style>
</head>
<body>

<!-- Falling stars -->
<div class="stars" id="stars-container"></div>

<div class="card">
    <div class="check-circle">
        <img src="https://cdn.pixabay.com/animation/2022/12/05/10/47/10-47-58-930_512.gif" alt="Success">
    </div>
    <h2>Payment Successful!</h2>
    <div class="tagline-en" id="tagline-en"></div>
    <div class="tagline-hi" id="tagline-hi"></div>
    <div class="countdown" id="countdown">3</div>
    <p>You will be redirected shortly...</p>
</div>

<script>
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });

    const starsContainer = document.getElementById('stars-container');
    for (let i = 0; i < 30; i++) {
        const star = document.createElement('div');
        star.classList.add('star');
        star.style.left = Math.random() * 100 + 'vw';
        star.style.animationDuration = (3 + Math.random() * 3) + 's';
        star.style.animationDelay = Math.random() * 5 + 's';
        starsContainer.appendChild(star);
    }

    const taglines = [
        ["You're now one step closer to success!", "अब आप सफलता की ओर एक कदम और आगे बढ़ चुके हैं।"],
        ["Course unlocked. Let the learning begin!", "कोर्स अनलॉक हो गया है। अब सीखना शुरू करें।"],
        ["Your journey to excellence starts now.", "आपकी उत्कृष्टता की यात्रा अब शुरू होती है।"],
        ["Access granted! Dive into your course.", "एक्सेस मिल चुका है! अब कोर्स में उतरें।"],
        ["Well done! You're investing in your future.", "शाबाश! आपने अपने भविष्य में निवेश किया है।"],
        ["You're in! Let's make every minute count.", "आप अंदर आ गए हैं! हर मिनट मायने रखेगा।"],
        ["Learning unlocked. Greatness awaits!", "सीखने का दरवाज़ा खुल गया है। महानता इंतज़ार कर रही है।"],
        ["Success starts with this step.", "सफलता इसी कदम से शुरू होती है।"],
        ["Your dedication is inspiring.", "आपकी लगन प्रेरणादायक है।"],
        ["Time to rise and shine!", "अब वक्त है चमकने का।"]
    ];

    const rand = Math.floor(Math.random() * taglines.length);
    document.getElementById('tagline-en').textContent = taglines[rand][0];
    document.getElementById('tagline-hi').textContent = taglines[rand][1];

    let count = 4;
    const countdownEl = document.getElementById('countdown');

    function animateCountdown() {
        countdownEl.classList.remove("zoomed");
        void countdownEl.offsetWidth;
        countdownEl.classList.add("zoomed");
    }

    const interval = setInterval(() => {
        count--;
        if (count === 0) {
            clearInterval(interval);
            window.location.href = "https://front.online2study.in/";
        } else {
            countdownEl.textContent = count;
            animateCountdown();
        }
    }, 1000);
</script>
</html>
HTML;
                } else {
                    return "<h2>Payment Verification Failed</h2><p>" . ($result['message'] ?? 'Unknown error') . "</p>";
                }
            } catch (\Exception $e) {
                return "<h2>Error fetching payment details</h2><p>" . $e->getMessage() . "</p>";
            }
        }

        return "<h2>Invalid Request</h2>";
    }

    public function handleWebhook(Request $request)
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        try {
            $api = new Api($this->razorpay_key, $this->razorpay_secret);
            $api->utility->verifyWebhookSignature($payload, $webhookSignature, $webhookSecret);
        } catch (\Exception $e) {
            \Log::error('Razorpay Webhook Signature Verification Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        if ($event['event'] === 'payment.captured') {
            $payment = $event['payload']['payment']['entity'];

            if ($payment['status'] !== 'captured') {
                return response()->json(['status' => 'ignored']);
            }

            // Extract notes or metadata if available
            $notes = $payment['notes'] ?? [];

            if (!isset($notes['user_id']) || !isset($notes['course_id'])) {
                \Log::warning('Razorpay Webhook: Missing notes in payment entity', ['payment_id' => $payment['id']]);
                return response()->json(['status' => 'ok']);
            }

            // Idempotency: Avoid duplicates
            $exists = Payment::where('payment_id', $payment['id'])->exists();
            if ($exists) {
                return response()->json(['status' => 'already_processed']);
            }

            $data = [
                'payment_id' => $payment['id'],
                'currency' => $payment['currency'],
                'status' => $payment['status'],
                'user_id' => $notes['user_id'],
                'course_id' => $notes['course_id'],
                'plan_type' => $notes['plan_type'] ?? 'monthly',
                'method' => $payment['method'] ?? 'unknown',
                'source' => 'razorpay',
            ];

            // Use the existing store logic to save payment and update course
            $storeRequest = new Request($data);
            $this->store($storeRequest);

            // Send Notification
            $this->notificationService->send([
                'user_id' => $notes['user_id'],
                'title' => 'Payment Successful',
                'message' => "Your payment of ₹" . ($payment['amount'] / 100) . " has been confirmed.",
                'source' => 'razorpay',
                'type' => 'payment',
                'action_url' => '/Payments'
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        $payments = Payment::with(['user', 'course'])->get();
        $userCoins = UserCoin::with('user')->get();
        $mergedData = $payments->map(function ($item) {
            return [
                'source' => $item->source ?? 'Payment',
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
            'currency' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|exists:google_users,id',
            'course_id' => 'required|exists:courses,id',
            'plan_type' => 'required|in:monthly,semi_annual,annual',
            'method' => 'nullable|string',
            'card_last4' => 'nullable|string',
            'card_network' => 'nullable|string',
            'vpa' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        // Get user
        $user = GoogleUser::find($validated['user_id']);

        // Get course
        $course = Course::where('is_paid', true)
            ->find($validated['course_id']);

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

        $offer = Offer::whereJsonContains('course', (string)$course->id)
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
                'meta_data' => json_encode([
                    'provider' => 'razorpay',
                    'payment_id' => $validated['payment_id']
                ])
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
            'user_id' => 'required|exists:google_users,id',
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
        $offer = Offer::whereJsonContains('course', (string)$course->id)
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
