<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletHistory;
use App\Models\GoogleUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Wallet API",
 *      description="API documentation for Wallet transactions",
 *      @OA\Contact(
 *          email="support@example.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 */
class WalletHistoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/wallet/history",
     *      summary="Get all wallet histories",
     *      tags={"Wallet"},
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function index()
    {
        $walletHistories = WalletHistory::with('user:id,name')
            ->get();

        return response()->json(['status' => true, 'data' => $walletHistories], 200);
    }

    public function webindex()
    {
        $walletHistories = WalletHistory::with('user:id,name')->paginate(10);
        return view('WalletHistory.index', compact('walletHistories'));
    }

    /**
     * @OA\Post(
     *      path="/api/wallet/add",
     *      summary="Add coins to the wallet",
     *      tags={"Wallet"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"google_user_id", "coin", "method", "date", "transaction_id", "amount"},
     *              @OA\Property(property="google_user_id", type="integer", example=1),
     *              @OA\Property(property="coin", type="integer", example=100),
     *              @OA\Property(property="method", type="string", example="PayPal"),
     *              @OA\Property(property="date", type="string", format="date", example="2024-03-31"),
     *              @OA\Property(property="transaction_id", type="string", example="txn_123456"),
     *              @OA\Property(property="amount", type="number", format="float", example=10.50)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Wallet credited successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation errors"
     *      )
     * )
     */
    public function walletAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_user_id' => 'required|exists:google_users,id',
            'coin' => 'required|integer|min:1',
            'method' => 'required|string',
            'date' => 'required|date',
            'transaction_id' => 'required|string|unique:wallet_histories',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            $user = GoogleUser::find($request->google_user_id);

            $user->coins += $request->coin;

            $user->save();

            $walletHistory = WalletHistory::create([
                'google_user_id' => $request->google_user_id,
                'coin' => $request->coin,
                'method' => $request->method,
                'date' => $request->date,
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'status' => 'Success',
                'payment_type' => 'credit'
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Wallet credited successfully', 'data' => $walletHistory], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/wallet/charges",
     *      summary="Deduct coins from the wallet",
     *      tags={"Wallet"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"google_user_id", "coin", "method", "date", "transaction_id", "amount"},
     *              @OA\Property(property="google_user_id", type="integer", example=1),
     *              @OA\Property(property="coin", type="integer", example=50),
     *              @OA\Property(property="method", type="string", example="Bank Transfer"),
     *              @OA\Property(property="date", type="string", format="date", example="2024-03-31"),
     *              @OA\Property(property="transaction_id", type="string", example="txn_654321"),
     *              @OA\Property(property="amount", type="number", format="float", example=5.25)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Wallet debited successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Insufficient coins or validation errors"
     *      )
     * )
     */
    public function walletCharges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'google_user_id' => 'required|exists:google_users,id',
            'coin' => 'required|integer|min:1',
            'method' => 'required|string',
            'date' => 'required|date',
            'transaction_id' => 'required|string|unique:wallet_histories',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {

            $user = GoogleUser::find($request->google_user_id);


            if ($user->coins < $request->coin) {
                return response()->json(['status' => false, 'message' => 'Insufficient coins'], 400);
            }


            $user->coins -= $request->coin;
            $user->save();


            $walletHistory = WalletHistory::create([
                'google_user_id' => $request->google_user_id,
                'coin' => $request->coin,
                'method' => $request->method,
                'date' => $request->date,
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'status' => 'Success',
                'payment_type' => 'debit'
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Wallet debited successfully', 'data' => $walletHistory], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }
}
