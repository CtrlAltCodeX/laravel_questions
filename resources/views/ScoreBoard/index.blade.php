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
                <th scope="col" class="px-6 py-3">Course Name</th>
                <th scope="col" class="px-6 py-3">Learning Progress</th>
                <th scope="col" class="px-6 py-3">Quize Paractice</th>
                <th scope="col" class="px-6 py-3">Mock Test CBT</th>
                <th scope="col" class="px-6 py-3">Question Bank</th>
            </tr>
        </thead>
        <tbody>
            @forelse($UserCourses as $index => $UserCourse)
            <tr class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700">
                <td class="px-6 py-4">{{ $index + 1 }}</td>
                <td class="px-6 py-4">{{ $UserCourse->user->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $UserCourse->course->category->language->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $UserCourse->course->category->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $UserCourse->course->name ?? 'N/A' }}</td>

                <td class="px-6 py-4">--</td>
                <td class="px-6 py-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="quiz-eye cursor-pointer mx-auto"
                        data-user="{{ $UserCourse->id }}"
                        width="32" height="32" fill="blue" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14m0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16" />
                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 .935-.252 1.064-.598l.088-.416c.073-.34.134-.569.288-.569.165 0 .21.207.138.577l-.088.415c-.194.897-.728 1.319-1.532 1.319-1.2 0-1.785-.805-1.532-2.084l.738-3.468c.194-.897.728-1.319 1.532-1.319.545 0 .935.252 1.064.598l.088.416c.073.34.134.569.288.569.165 0 .21-.207.138-.577l-.088-.415c-.194-.897-.728-1.319-1.532-1.319zm-.93-2.588a.905.905 0 1 1 0 1.81.905.905 0 0 1 0-1.81" />
                    </svg>
                </td>
                <td class="px-6 py-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="mock-test-eye cursor-pointer mx-auto"
                        data-user="{{ $UserCourse->id }}"
                        width="32" height="32" fill="blue" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14m0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16" />
                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 .935-.252 1.064-.598l.088-.416c.073-.34.134-.569.288-.569.165 0 .21.207.138.577l-.088.415c-.194.897-.728 1.319-1.532 1.319-1.2 0-1.785-.805-1.532-2.084l.738-3.468c.194-.897.728-1.319 1.532-1.319.545 0 .935.252 1.064.598l.088.416c.073.34.134.569.288.569.165 0 .21-.207.138-.577l-.088-.415c-.194-.897-.728-1.319-1.532-1.319zm-.93-2.588a.905.905 0 1 1 0 1.81.905.905 0 0 1 0-1.81" />
                    </svg>
                </td>
                <td class="px-6 py-4 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="question-bank-eye cursor-pointer mx-auto"
                        data-user="{{ $UserCourse->id }}"
                        width="32" height="32" fill="blue" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14m0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16" />
                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 .935-.252 1.064-.598l.088-.416c.073-.34.134-.569.288-.569.165 0 .21.207.138.577l-.088.415c-.194.897-.728 1.319-1.532 1.319-1.2 0-1.785-.805-1.532-2.084l.738-3.468c.194-.897.728-1.319 1.532-1.319.545 0 .935.252 1.064.598l.088.416c.073.34.134.569.288.569.165 0 .21-.207.138-.577l-.088-.415c-.194-.897-.728-1.319-1.532-1.319zm-.93-2.588a.905.905 0 1 1 0 1.81.905.905 0 0 1 0-1.81" />
                    </svg>
                </td>


            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center">No data available</td>
            </tr>
            @endforelse

        </tbody>

    </table>
</div>

<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="
        background-color: white; 
        border-radius: 10px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        width: 50%; 
        max-height: 90vh; 
           min-height: 50vh; 
        margin: auto; 
        padding: 24px; 
        position: relative; 
        overflow-y: auto; /* Make content scrollable if it exceeds max height */
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;"></h2>
            <button id="closeModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>

        <table class="w-full text-sm text-left text-gray-500 border">
            <thead class="bg-gray-100">
                <tr id="quizTableHead"></tr>
            </thead>
            <tbody id="quizTableBody">
                <tr>
                    <td colspan="6" class="text-center py-3">Loading...</td>
                </tr>
            </tbody>
        </table>


    </div>
</div>
@endsection

@push('scripts')
@include('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const quizTableHeadings = ["#", "Subject", "Topic", "Percentage", "Attempt", "Date/Time"];
        const questionBankTableHeadings = ["#", "Subject", "Topic", "Count", "Date/Time"];

        function renderTableHead(headings) {
            document.getElementById("quizTableHead").innerHTML =
                headings.map(h => `<th class="px-4 py-2">${h}</th>`).join("");
        }

        function handleEyeClick(selector, options) {
            document.querySelectorAll(selector).forEach(icon => {
                icon.addEventListener("click", function() {
                    const userId = this.getAttribute("data-user");

                    renderTableHead(options.headings);
                    document.getElementById("modal").style.display = "flex";
                    document.getElementById('modalTitle').innerText = options.title;
                    document.getElementById("quizTableBody").innerHTML = `
                    <tr><td colspan="6" class="text-center py-3">Loading...</td></tr>
                `;


                    fetch(`${options.url}/${userId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.data?.length) {
                                document.getElementById("quizTableBody").innerHTML =
                                    data.data.map((item, index) => options.renderRow(item, index)).join("");
                            } else {
                                document.getElementById("quizTableBody").innerHTML = `
                                <tr><td colspan="6" class="text-center py-3">No data found</td></tr>
                            `;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById("quizTableBody").innerHTML = `
                            <tr><td colspan="6" class="text-center py-3 text-red-500">Error fetching data</td></tr>
                        `;
                        });
                });
            });
        }

        // Question Bank
        handleEyeClick(".question-bank-eye", {
            headings: questionBankTableHeadings,
            url: "/question-bank-count-AllData",
            title: "Question Bank Data",
            renderRow: (quiz, index) => `
            <tr class="border-b">
                <td class="px-4 py-2">${index + 1}</td>
                <td class="px-4 py-2">${quiz.subject?.name ?? 'N/A'}</td>
                <td class="px-4 py-2">${quiz.topic?.name ?? 'N/A'}</td>
                <td class="px-4 py-2">${quiz.count}</td>
                <td class="px-4 py-2">${new Date(quiz.created_at).toLocaleString()}</td>
            </tr>
        `
        });

        // Quiz Practice
        handleEyeClick(".quiz-eye", {
            headings: quizTableHeadings,
            url: "/quize-practice",
            title: "Quiz Practice Data",
            renderRow: (quiz, index) => `
            <tr class="border-b">
                <td class="px-4 py-2">${index + 1}</td>
                <td class="px-4 py-2">${quiz.subject_name ?? 'N/A'}</td>
                <td class="px-4 py-2">${quiz.topic ?? 'N/A'}</td>
             <td class="px-4 py-2">${parseInt(quiz.percentage)}%</td>
              <td class="px-4 py-2">${quiz.attempt}</td>

                <td class="px-4 py-2">${new Date(quiz.created_at).toLocaleString()}</td>
            </tr>
        `
        });


        // Mock Test
        handleEyeClick(".mock-test-eye", {
            headings: ["#", "Sub-Category", "Right Ans.", "Wrong Ans.", "Attempt", "Time Taken", "Date/Time"],
            url: "/mock-test",
            title: "Mock Test Data",
            renderRow: (quiz, index) => `
     <tr class="border-b">
            <td class="px-4 py-2">${index + 1}</td>
            <td class="px-4 py-2">${quiz.sub_category?.name ?? 'N/A'}</td>
            <td class="px-4 py-2">${quiz.right_answer}</td>
            <td class="px-4 py-2">${quiz.wrong_answer}</td>
            <td class="px-4 py-2">${quiz.attempt}</td>
            <td class="px-4 py-2">${quiz.time_taken} sec</td>
            <td class="px-4 py-2">${new Date(quiz.created_at).toLocaleString()}</td>
        </tr>
    `
        });


        // Close modal
        document.getElementById("closeModal").addEventListener("click", function() {
            document.getElementById("modal").style.display = "none";
        });

    });
</script>

@endpush