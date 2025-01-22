<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GoogleUser;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $categoriesCount = Category::count();

        $subCategoriesCount = SubCategory::count();

        $subjectsCount = Subject::count();

        $topicsCount = Topic::count();

        $questionsCount = Question::count();

        $usersCount = GoogleUser::count();

        return view('dashboard.index', compact(
            'categoriesCount',
            'subCategoriesCount',
            'subjectsCount',
            'topicsCount',
            'questionsCount',
            'usersCount'
        ));
    }
}
