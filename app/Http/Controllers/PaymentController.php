<?php
// app/Http/Controllers/Api/PaymentController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|unique:payments',
            'amount'     => 'required|integer',
            'currency'   => 'required|string',
            'status'     => 'required|string',
            'email'      => 'nullable|email',
            'contact'    => 'nullable|string',
        ]);

        $payment = Payment::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment saved successfully',
            'data' => $payment
        ]);
    }
}
