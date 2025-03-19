<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletHistory;
use App\Models\GoogleUser; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class WalletHistoryController extends Controller
{
   
    public function index()
    {
        $walletHistories = WalletHistory::with('user:id,name')->get();
        return response()->json(['status' => true, 'data' => $walletHistories], 200);
    }

    public function webindex()
    {
        $walletHistories = WalletHistory::with('user:id,name')->paginate(10);
        return view('WalletHistory.index', compact('walletHistories'));
    }

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
