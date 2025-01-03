<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\User;

class DashboardController extends Controller
{
    public function totalcount()
    {
       
        // Fetch total counts
        $categoriesCount = Category::count();
        $subCategoriesCount = SubCategory::count();
        $subjectsCount = Subject::count();
        $topicsCount = Topic::count();
        $questionsCount = Question::count();
        $usersCount = User::count();

        // Pass data to the view
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
