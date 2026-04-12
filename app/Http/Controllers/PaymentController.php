<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\GoogleUser;

class PaymentController extends Controller
{


    public function getUserPayments(Request $request, $userId)
    {
        // 1. Check User existence
        $user = GoogleUser::find($userId);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // 2. Build Query
        $query = Payment::with('course')->where('user_id', $userId);

        // 3. Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        // Allowed sort fields
        if (!in_array($sort, ['id', 'amount', 'created_at', 'status'])) {
            $sort = 'created_at';
        }
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'desc';
        }

        $query->orderBy($sort, $order);

        // 4. Pagination
        $perPage = $request->get('per_page', 10);
        $payments = $query->paginate($perPage);

        // 5. Format Response
        $data = [
            'current_page' => $payments->currentPage(),
            'last_page' => $payments->lastPage(),
            'per_page' => $payments->perPage(),
            'total' => $payments->total(),
            'from' => $payments->firstItem(),
            'to' => $payments->lastItem(),
            'payments' => $payments->getCollection()->map(function ($payment) {
            return [
            'id' => $payment->id,
            'payment_id' => $payment->payment_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'course_id' => $payment->course_id,
            'course_name' => $payment->course->name ?? 'N/A',
            'plan_type' => $payment->plan_type,
            'method' => $payment->method,
            'vpa' => $payment->vpa,
            'source' => $payment->source,
            'date' => $payment->created_at->toDateTimeString(),
            'created_at' => $payment->created_at,
            ];
        }),
        ];

        return response()->json([
            'success' => true,
            'message' => 'User payment history retrieved successfully',
            'data' => $data
        ]);
    }
}