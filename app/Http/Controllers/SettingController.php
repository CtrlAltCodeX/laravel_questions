<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    /**
     * @OA\Get(
     *     path="/api/coins",
     *     summary="Get application settings",
     *     tags={"Settings"},
     *     @OA\Response(
     *         response=200,
     *         description="Settings fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Settings fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="site_name", type="string", example="My App"),
     *                 @OA\Property(property="logo", type="string", example="logo.png")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No settings found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No settings found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function getSettingsApi()
    {
        $setting = Setting::first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'No settings found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings fetched successfully',
            'data' => $setting
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'refer_coin' => 'required|integer|min:0',
            'welcome_coin' => 'required|integer|min:0',
        ]);

        if ($setting = Setting::first()) {
            // Update existing record
            $setting->update([
                'refer_coin' => $request->refer_coin,
                'welcome_coin' => $request->welcome_coin,
            ]);
        } else {
            // Create new record if none exists
            Setting::create([
                'refer_coin' => $request->refer_coin,
                'welcome_coin' => $request->welcome_coin,
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully');
    }
}
