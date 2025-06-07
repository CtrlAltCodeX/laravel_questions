<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCourse;
use App\Models\Course;

class UserCourseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/courses/{userId}",
     *     summary="Get all courses assigned to a user",
     *     description="Fetches all courses assigned to the specified user, including basic course details.",
     *     operationId="getUserCourses",
     *     tags={"Courses"},
     *
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with user's courses",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="course_id", type="integer", example=3),
     *                     @OA\Property(
     *                         property="course_detail",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="Algebra 101"),
     *                         @OA\Property(property="language_id", type="integer", example=2),
     *                         @OA\Property(property="category_id", type="integer", example=5),
     *                         @OA\Property(property="sub_category_id", type="integer", example=10),
     *                         @OA\Property(property="subject_id", type="integer", example=12),
     *                         @OA\Property(property="status", type="boolean", example=true),
     *                         @OA\Property(property="subscription", type="string", example="monthly"),
     *                         @OA\Property(property="banner", type="string", example="algebra-banner.jpg")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
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
