<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\QuizSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizSettingController extends Controller
{
    public function getSettings($userId, $courseId)
    {
        $settings = QuizSetting::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$settings) {
            // Default settings
            return response()->json([
                'quizSettings' => [
                    'quizLimit' => 20,
                    'timer' => 30,
                    'autoNext' => true,
                    'sound' => false,
                    'shuffle' => true
                ]
            ]);
        }

        return response()->json([
            'quizSettings' => [
                'quizLimit' => $settings->quiz_limit,
                'timer' => $settings->timer,
                'autoNext' => (bool) $settings->auto_next,
                'sound' => (bool) $settings->sound,
                'shuffle' => (bool) $settings->shuffle
            ]
        ]);
    }

    public function updateSettings(Request $request, $userId, $courseId)
    {
        $validator = Validator::make($request->all(), [
            'quizSettings' => 'required|array',
            'quizSettings.quizLimit' => 'required|integer',
            'quizSettings.timer' => 'required|integer',
            'quizSettings.autoNext' => 'required|boolean',
            'quizSettings.sound' => 'required|boolean',
            'quizSettings.shuffle' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->input('quizSettings');

        $settings = QuizSetting::updateOrCreate(
            [
                'user_id' => $userId, 
                'course_id' => $courseId
            ],
            [
                'quiz_limit' => $data['quizLimit'],
                'timer' => $data['timer'],
                'auto_next' => $data['autoNext'],
                'sound' => $data['sound'],
                'shuffle' => $data['shuffle'],
            ]
        );

        return response()->json([
            'quizSettings' => [
                'quizLimit' => $settings->quiz_limit,
                'timer' => $settings->timer,
                'autoNext' => (bool) $settings->auto_next,
                'sound' => (bool) $settings->sound,
                'shuffle' => (bool) $settings->shuffle
            ]
        ]);
    }
}
