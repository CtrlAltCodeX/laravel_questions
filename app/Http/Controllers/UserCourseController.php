<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCourse;
use App\Models\Course;

class UserCourseController extends Controller
{

   public function getUserCourses($userId)
{
    $userCourses = UserCourse::where('user_id', $userId)->get();

    $data = $userCourses->map(function ($userCourse) {
        $course = Course::find($userCourse->course_id);

        return [
            'user_id'         => $userCourse->user_id,
            'course_id'       => $userCourse->course_id,
            'plan_type'       => $userCourse->subscription_type,
            'valid_from'      => $userCourse->valid_from,
            'valid_to'        => $userCourse->valid_to,
            'course_detail'   => $course ? [
                'id'            => $course->id,
                'name'          => $course->name,
                'language_id'   => $course->language_id,
                'category_id'   => $course->category_id,
                'sub_category_id'=> $course->sub_category_id,
                'subject_id'    => $course->subject_id,
                'status'        => $course->status,
                'subscription'  => $course->subscription,
                'banner'        => $course->banner
            ] : null,
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
}

    public function assignCourseToUser(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required',
        'course_id' => 'required',
        'subscription_type' => 'required|string',
        'valid_from' => 'required|date',
        'valid_to' => 'required|date|after_or_equal:valid_from',
    ]);

    $userCourse = UserCourse::create($validated);

    return response()->json([
        'status' => true,
        'message' => 'Course assigned to user successfully.',
        'data' => $userCourse
    ]);
}

}
