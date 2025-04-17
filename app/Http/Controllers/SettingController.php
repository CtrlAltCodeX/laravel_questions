<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Fetch the first setting record
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    public function getSettingsApi()
{
    $setting = Setting::first();

    if ($setting) {
        return response()->json([
            'success' => true,
            'message' => 'Settings fetched successfully',
            'data' => $setting
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'No settings found',
            'data' => null
        ], 404);
    }
}


    public function store(Request $request)
    {
        $request->validate([
            'refer_coin' => 'required|integer|min:0',
            'welcome_coin' => 'required|integer|min:0',
        ]);

        // Check if a setting record exists
        $setting = Setting::first();

        if ($setting) {
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
