@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Categories -->
        <div class="bg-blue-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Categories</h5>
            <p class="text-4xl font-semibold">{{ $categoriesCount }}</p>
        </div>
        <!-- Total SubCategories -->
        <div class="bg-green-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total SubCategories</h5>
            <p class="text-4xl font-semibold">{{ $subCategoriesCount }}</p>
        </div>
        <!-- Total Subjects -->
        <div class="bg-yellow-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Subjects</h5>
            <p class="text-4xl font-semibold">{{ $subjectsCount }}</p>
        </div>
        <!-- Total Topics -->
        <div class="bg-teal-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Topics</h5>
            <p class="text-4xl font-semibold">{{ $topicsCount }}</p>
        </div>
        <!-- Total Questions -->
        <div class="bg-red-500 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Questions</h5>
            <p class="text-4xl font-semibold">{{ $questionsCount }}</p>
        </div>
        <!-- Total Users -->
        <div class="bg-gray-700 text-white rounded-lg shadow-md p-6 text-center">
            <h5 class="text-lg font-bold">Total Users</h5>
            <p class="text-4xl font-semibold">{{ $usersCount }}</p>
        </div>
    </div>
</div>

@endsection