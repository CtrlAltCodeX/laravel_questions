<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCourse;
use App\Models\Course;

class UserCourseController extends Controller
{
    /**
     * Get all courses purchased by a user
     */
    public function getUserCourses($userId)
    {
        $userCourses = UserCourse::where('user_id', $userId)->get();

        $data = $userCourses->map(function ($userCourse) {
            $course = Course::find($userCourse->course_id);

            return [
                'user_id' => $userCourse->user_id,
                'course_id' => $userCourse->course_id,
                'course_detail' => $course ? [
                    'id' => $course->id,
                    'name' => $course->name,
                    'language_id' => $course->language_id,
                    'category_id' => $course->category_id,
                    'sub_category_id' => $course->sub_category_id,
                    'subject_id' => $course->subject_id,
                    'status' => $course->status,
                    'subscription' => $course->subscription,
                    'banner' => $course->banner
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
