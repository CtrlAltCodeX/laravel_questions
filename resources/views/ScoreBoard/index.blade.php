@extends('layouts.app')

@section('title', 'Scoreboard')

@section('content')
<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Scoreboard</h1>
    <div class="flex justify-end items-center gap-2">
        <input type="text" id="searchFilter" placeholder="Search Scores..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
    </div>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="scoreboardTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3"> Name</th>
                <th scope="col" class="px-6 py-3">Language Name</th>
                <th scope="col" class="px-6 py-3">Category Name</th>
                <th scope="col" class="px-6 py-3">Sub-Category Name</th>
                <th scope="col" class="px-6 py-3">Learning Progress</th>
                <th scope="col" class="px-6 py-3">Practice Progress</th>
                <th scope="col" class="px-6 py-3">Test Rank</th>
            </tr>
        </thead>
        <tbody>
    @forelse($ScoreBoards as $index => $ScoreBoard)
    <tr class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700">
        <td class="px-6 py-4">{{ $index + 1 }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->user->name ?? 'N/A' }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->subCategory->category->language->name ?? 'N/A' }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->subCategory->category->name ?? 'N/A' }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->subCategory->name ?? 'N/A' }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->total_videos }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->quiz_practice }}</td>
        <td class="px-6 py-4">{{ $ScoreBoard->test_rank }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="px-6 py-4 text-center">No scores available</td>
    </tr>
    @endforelse
</tbody>

    </table>
</div>
@endsection
