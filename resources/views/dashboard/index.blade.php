@extends('layouts.app')

@section('content')
<div class="container mt-0">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 rounded shadow-lg">
                <div class="card-body text-center bg-primary text-white">
                    <h5 class="card-title fw-bold">Total Categories</h5>
                    <p class="card-text display-4">{{ $categoriesCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 rounded shadow-lg">
                <div class="card-body text-center bg-success text-white">
                    <h5 class="card-title fw-bold">Total SubCategories</h5>
                    <p class="card-text display-4">{{ $subCategoriesCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 rounded shadow-lg">
                <div class="card-body text-center bg-warning text-white">
                    <h5 class="card-title fw-bold">Total Subjects</h5>
                    <p class="card-text display-4">{{ $subjectsCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 rounded shadow-lg">
                <div class="card-body text-center bg-info text-white">
                    <h5 class="card-title fw-bold">Total Topics</h5>
                    <p class="card-text display-4">{{ $topicsCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 rounded shadow-lg">
                <div class="card-body text-center bg-danger text-white">
                    <h5 class="card-title fw-bold">Total Questions</h5>
                    <p class="card-text display-4">{{ $questionsCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 rounded shadow-lg">
                <div class="card-body text-center bg-secondary text-white">
                    <h5 class="card-title fw-bold">Total Users</h5>
                    <p class="card-text display-4">{{ $usersCount }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
