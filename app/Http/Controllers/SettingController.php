<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\SettingQuiz;
use App\Models\SettingCbt;

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

    public function saveQuiz(Request $request)
{
    $request->validate([
        'quiz_language_type' => 'required|in:single,multiple',
        'quiz_languages' => 'required|array|min:1|max:2',
        'category_id' => 'required|exists:categories,id',
        'sub_category_id' => 'required|exists:sub_categories,id',
        'question_limit' => 'required|integer|min:1',
    ]);

    $exists = SettingQuiz::where('category_id', $request->category_id)
        ->where('sub_category_id', $request->sub_category_id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Quiz already exists for this Category and Subcategory.');
    }

    SettingQuiz::create([
        'language_type' => $request->quiz_language_type,
        'language_ids' => $request->quiz_languages,
        'category_id' => $request->category_id,
        'sub_category_id' => $request->sub_category_id,
        'question_limit' => $request->question_limit,
    ]);

    return redirect()->back()->with('success', 'Quiz saved successfully.');
}



public function saveCbt(Request $request)
{
    $request->validate([
        'cbt_language_type' => 'required|in:single,multiple',
        'cbt_languages' => 'required|array|min:1|max:2',
        'category_id' => 'required|exists:categories,id',
        'sub_category_id' => 'required|exists:sub_categories,id',
        'question_limit' => 'nullable|integer|min:1',
    ]);

    $exists = SettingCbt::where('category_id', $request->category_id)
        ->where('sub_category_id', $request->sub_category_id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'CBT already exists for this Category and Subcategory.');
    }

    SettingCbt::create([
        'language_type' => $request->cbt_language_type,
        'language_ids' => $request->cbt_languages,
        'category_id' => $request->category_id,
        'sub_category_id' => $request->sub_category_id,
        'question_limit' => $request->question_limit,
    ]);

    return redirect()->back()->with('success', 'CBT saved successfully.');
}


}
