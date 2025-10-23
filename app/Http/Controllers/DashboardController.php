<?php

namespace App\Http\Controllers;

use App\Exports\UserAnalyticsExport;
use App\Models\Category;
use App\Models\GoogleUser;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Video;
use App\Models\UserCoin;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $categoriesCount = Category::count();

        $subCategoriesCount = SubCategory::count();

        $subjectsCount = Subject::count();

        $topicsCount = Topic::count();


        $questionsCount = Question::count();

        $videoCount = Video::count();
        $reportCount = Report::count();


        $usersCount = GoogleUser::count();

        return view('dashboard.index', compact(
            'categoriesCount',
            'subCategoriesCount',
            'subjectsCount',
            'topicsCount',
            'questionsCount',
            'usersCount',
            'videoCount',
            'reportCount'

        ));
    }


    public function getUserAnalytics($range)
    {
        $startDate = null;

        if ($range === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        } elseif ($range === '1month') {
            $startDate = Carbon::now()->subMonth()->startOfDay();
        }

        $query = GoogleUser::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        $dates = collect();

        $minDate = GoogleUser::min('created_at');
        if (!$minDate) {
            return response()->json([]);
        }

        $current = Carbon::parse($startDate ?? $minDate)->startOfDay();
        $end = Carbon::now()->startOfDay();

        while ($current->lte($end)) {
            $dates->push([
                'date' => $current->toDateString(),
                'total' => $query[$current->toDateString()] ?? 0
            ]);
            $current->addDay();
        }

        if ($range === 'all') {
            $dates = $dates->filter(fn($item) => $item['total'] > 0)->values();
        }

        return response()->json($dates);
    }


    public function exportUserAnalytics(Request $request)
    {
        $range = $request->get('range', '7days'); // default to 7days
        return Excel::download(new UserAnalyticsExport($range), 'user_analytics.xlsx');
    }



    public function getUserPaymentAnalytics($range)
    {
        $startDate = null;

        if ($range === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        } elseif ($range === '1month') {
            $startDate = Carbon::now()->subMonth()->startOfDay();
        }

        // ✅ Courses purchased count per date (from payments table)
        $courseData = Payment::selectRaw('DATE(created_at) as date, COUNT(course_id) as total')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->whereNotNull('course_id') // ensure it's a course purchase
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        // ✅ Coins added per date (from user_coins table)
        $coinData = UserCoin::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        // ✅ Find the earliest date among both datasets
        $minDate = collect([$courseData->keys()->min(), $coinData->keys()->min()])
            ->filter()
            ->min();

        if (!$minDate) {
            return response()->json([]);
        }

        $dates = collect();
        $current = Carbon::parse($startDate ?? $minDate)->startOfDay();
        $end = Carbon::now()->startOfDay();

        while ($current->lte($end)) {
            $date = $current->toDateString();
            $dates->push([
                'date' => $date,
                'courses' => $courseData[$date] ?? 0,
                'coins' => $coinData[$date] ?? 0,
            ]);
            $current->addDay();
        }

        if ($range === 'all') {
            $dates = $dates->filter(fn($item) => $item['courses'] > 0 || $item['coins'] > 0)->values();
        }

        return response()->json($dates);
    }
}
