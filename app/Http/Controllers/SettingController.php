<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SubCategory;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        $languages = Language::all();

        $categories = Category::all();

        $sub_categories = SubCategory::all();

        return view('settings.index', compact('setting', 'languages', 'categories', 'sub_categories'));
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
