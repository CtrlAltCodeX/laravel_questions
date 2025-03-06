@extends('layouts.app')
@section('title', 'Question')
@php
$dropdown_list = [
'Select Language' => $languages,
'Select Category' => $categories,
'Select Sub Category' => $subcategories ?? [],
'Select Subject' => $subjects ?? [],
'Select Topic' => $topics ?? [],
];

$levels = [
'1' => 'Easy',
'2' => 'Medium',
'3' => 'Hard',
]
@endphp
<style>
    #columnSelectDropdown {
        max-height: 200px;
        overflow-y: auto;
    }

    #columnSelectDropdown input[type="checkbox"] {
        accent-color: #4f46e5;
        /* Customize checkbox color */
    }

    /* Ensure the dropdown is hidden by default */
    #columnSelectDropdown.hidden {
        display: none;
    }
</style>
@section('content')
<div class="flex flex-col">
    <div class="flex flex-col">
        <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white flex flex-col justify-between ">Question</h1>
    </div>
    <br />
    <div class="flex flex-col items-self-end gap-2">
        <div class="flex justify-end items-center gap-2">
            <form action="{{ route('question.index') }}" method="GET" id='data' class="mb-0 flex gap-2">
                <input type="hidden" value="{{ request()->per_page }}" name="per_page" />
                <input type="hidden" value="{{ request()->search }}" name="search" />
                <input type="hidden" value="{{ request()->sort }}" name="sort" />
                <input type="hidden" value="{{ request()->direction }}" name="direction" />

                @foreach ($dropdown_list as $moduleName => $module)
                @php
                $id = strtolower(Str::slug($moduleName, '_'));
                $moduleKey = Str::slug(strtolower(trim(explode('Select', $moduleName)[1])) . "_id", '_');
                $selectedValue = request()->input($moduleKey);
                @endphp
                <div>

                    <select name="{{ $moduleKey }}" id='{{ $id }}_select' class="{{ $id }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required-field">
                        <option value="">{{$moduleName}}</option>
                        @foreach($module as $item)
                        <option value="{{$item->id}}" {{ $selectedValue == $item->id ?  'selected' : '' }}>{{$item->name}}</option>
                        @endforeach
                    </select>
                    <div class="text-red-500 text-xs mt-1 validation-msg"></div>
                    @error('module.' . $moduleKey)
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach

                <button id="filter-btn" type="submit" class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Filter</button>
            </form>
        </div>

        <div class="flex justify-end items-center gap-2">
            <a href="/questions.xlsx" download="" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Download Sample</a>

            <button id="exportButton" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>

            <button id="importButton" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Import</button>

            <input type="file" id="importInput" name="file" class="form-control hidden" required>

            <button id="createButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Create
            </button>
        </div>

        <div class="flex items-end gap-y-5 justify-between items-center">
            <div class="">
                <button class="p-2 w-[40px] " id='delete-selected'>
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 10 2 L 9 3 L 3 3 L 3 5 L 4.109375 5 L 5.8925781 20.255859 L 5.8925781 20.263672 C 6.023602 21.250335 6.8803207 22 7.875 22 L 16.123047 22 C 17.117726 22 17.974445 21.250322 18.105469 20.263672 L 18.107422 20.255859 L 19.890625 5 L 21 5 L 21 3 L 15 3 L 14 2 L 10 2 z M 6.125 5 L 17.875 5 L 16.123047 20 L 7.875 20 L 6.125 5 z"></path>
                    </svg>
                </button>
            </div>
            <div class="flex items-end gap-2">
                <div class="relative inline-block w-full text-gray-700">
                    <form action="{{ route('question.index') }}" class="mb-0" method="GET" id='page'>
                        <input type="hidden" name="category_id" value="{{request()->category_id}}" />
                        <input type="hidden" name="sub_category_id" value="{{request()->sub_category_id}}" />
                        <input type="hidden" name="language_id" value="{{request()->language_id}}" />
                        <input type="hidden" name="subject_id" value="{{request()->subject_id}}" />
                        <input type="hidden" name="topic_id" value="{{request()->topic_id}}" />
                        <input type="hidden" value="{{ request()->sort }}" name="sort" />
                        <input type="hidden" value="{{ request()->direction }}" name="direction" />

                        <div class="flex gap-2">
                            <select class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer" name="per_page" id='per_page' style="width: 120px;">
                                <option value="">Per Page</option>
                                <option {{ request()->per_page == 50 ? 'selected' : '' }} value=50>50</option>
                                <option {{ request()->per_page == 100 ? 'selected' : '' }} value=100>100</option>
                                <option {{ request()->per_page == 200 ? 'selected' : '' }} value=200>200</option>
                                <option {{ request()->per_page == 500 ? 'selected' : '' }} value=500>500</option>
                            </select>

                            <div class="flex gap-2">
                                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search..." required name="search" value="{{ request()->search }}" />

                                <button class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" id='search'>Search</button>
                            </div>

                            <div>
                                <div id="columnSelectToggle" class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer">
                                    <svg viewBox="0 0 100 80" width="20" height="20">
                                        <rect width="100" height="20"></rect>
                                        <rect y="30" width="100" height="20"></rect>
                                        <rect y="60" width="100" height="20"></rect>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="columnSelectDropdown" class="absolute z-10 hidden w-[150px] right-[0px] mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
                        <div class="p-2">
                            <label class="block">
                                <input type="checkbox" value="id" class="mr-2">
                                ID
                            </label>
                            <label class="block">
                                <input type="checkbox" value="question_number" class="mr-2">
                                QNo.
                            </label>
                            <label class="block">
                                <input type="checkbox" value="language" class="mr-2">
                                Language
                            </label>
                            <label class="block">
                                <input type="checkbox" value="category" class="mr-2">
                                Category
                            </label>
                            <label class="block">
                                <input type="checkbox" value="subcategory" class="mr-2">
                                Sub-Category
                            </label>
                            <label class="block">
                                <input type="checkbox" value="subject" class="mr-2">
                                Subject
                            </label>
                            <label class="block">
                                <input type="checkbox" value="topic" class="mr-2">
                                Topic
                            </label>
                            <label class="block">
                                <input type="checkbox" value="link" class="mr-2">
                                External Photo
                            </label>
                            <label class="block">
                                <input type="checkbox" value="image" class="mr-2">
                                Internal Photo
                            </label>
                            <label class="block">
                                <input type="checkbox" value="question" class="mr-2">
                                Question
                            </label>
                            <label class="block">
                                <input type="checkbox" value="optionA" class="mr-2">
                                Option A
                            </label>
                            <label class="block">
                                <input type="checkbox" value="optionB" class="mr-2">
                                Option B
                            </label>
                            <label class="block">
                                <input type="checkbox" value="optionC" class="mr-2">
                                Option C
                            </label>
                            <label class="block">
                                <input type="checkbox" value="optionD" class="mr-2">
                                Option D
                            </label>
                            <label class="block">
                                <input type="checkbox" value="answer" class="mr-2">
                                Answer
                            </label>
                            <label class="block">
                                <input type="checkbox" value="level" class="mr-2">
                                Level
                            </label>
                            <label class="block">
                                <input type="checkbox" value="notes" class="mr-2">
                                Notes
                            </label>
                            <label class="block">
                                <input type="checkbox" value="action" class="mr-2">
                                Action
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="relative overflow-x-auto :rounded-lg mt-4">
    <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mb-4">
        <thead>
            <tr id="table-headers">
                <th scope="col" class="p-2">
                    <input type="checkbox" class="select-all" />
                </th>
                <th scope="col" class="p-2" data-column="id">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => $sortColumn == 'id' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        ID
                        @if ($sortColumn == 'id')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="question_number">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'question_number', 'direction' => $sortColumn == 'question_number' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        QNo.
                        @if ($sortColumn == 'question_number')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="language">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'language.name', 'direction' => $sortColumn == 'language.name' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        Language
                        @if ($sortColumn == 'language.name')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="category">Category</th>
                <th scope="col" class="p-2" data-column="subcategory">Sub-Category</th>
                <th scope="col" class="p-2" data-column="subject">Subject</th>
                <th scope="col" class="p-2" data-column="topic">Topic</th>
                <th scope="col" class="p-2" data-column="link" style="width: 70px;">External Photo</th>
                <th scope="col" class="p-2" data-column="image">Internal Photo</th>
                <th scope="col" class="p-2" data-column="question">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'question', 'direction' => $sortColumn == 'question' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        Question
                        @if ($sortColumn == 'question')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="optionA">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'option_a', 'direction' => $sortColumn == 'option_a' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        Option A
                        @if ($sortColumn == 'option_a')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="optionB">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'option_b', 'direction' => $sortColumn == 'option_b' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        Option B
                        @if ($sortColumn == 'option_b')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="optionC">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'option_c', 'direction' => $sortColumn == 'option_c' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        Option C
                        @if ($sortColumn == 'option_c')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="optionD">
                    <a href="{{ route('question.index', array_merge(request()->query(), ['sort' => 'option_d', 'direction' => $sortColumn == 'option_d' && $sortDirection == 'asc' ? 'desc' : 'asc'])) }}">
                        Option D
                        @if ($sortColumn == 'option_d')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="p-2" data-column="answer">Answer</th>
                <th scope="col" class="p-2" data-column="level">Level</th>
                <th scope="col" class="p-2" data-column="notes">Notes</th>
                <th scope="col" class="p-2" data-column="action">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
            <tr>
                <td class="p-2">
                    <input type="checkbox" class="select-item" value="{{ $question->id }}" />
                </td>
                <td class="p-2" data-column="id">{{ $question->id }}</td>
                <td class="p-2" data-column="question_number">{{ $question->question_number }}</td>
                <td class="p-2" data-column="language">{{ $question->language->name }}</td>
                <td class="p-2" data-column="category">{{ $question->category->name }}</td>
                <td class="p-2" data-column="subcategory">{{ $question->subCategory->name }}</td>
                <td class="p-2" data-column="subject">{{ $question->subject->name }}</td>
                <td class="p-2" data-column="topic">{{ $question->topic->name }}</td>
                <td class="p-2" data-column="link">
                    <img src="{{ $question->photo_link ? $question->photo_link : '/dummy.jpg' }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;" />
                    <!-- <a href="{{$question->photo_link ? $question->photo_link : '#'}}" target="_blank">{{$question->photo_link}}</a> -->
                </td>
                <td class="p-2" data-column="image">
                    <img src="{{ $question->photo ? 'storage/questions/'. $question->photo : '/dummy.jpg' }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;" />
                </td>
                <td class="p-2" data-column="question">{!! $question->question !!}</td>
                <td class="p-2" data-column="optionA">{{ $question->option_a }}</td>
                <td class="p-2" data-column="optionB">{{ $question->option_b }}</td>
                <td class="p-2" data-column="optionC">{{ $question->option_c }}</td>
                <td class="p-2" data-column="optionD">{{ $question->option_d }}</td>
                <td class="p-2" data-column="answer">{{ $question->answer }}</td>
                <td class="p-2" data-column="level">
                    @switch($question->level)
                    @case(1)
                    Easy
                    @break

                    @case(2)
                    Medium
                    @break

                    @case(3)
                    Hard
                    @break

                    @default

                    @endswitch
                </td>
                <td class="p-2" data-column="notes">{{ $question->notes }}</td>
                <td class="p-2 flex gap-2" data-column="action">
                    <button class="open-edit-modal" data-id="{{ $question->id }}"> <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg></button>
                    <form action="{{ route('question.destroy', $question->id) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="font-medium text-danger dark:text-danger-500 hover:underline" onclick="return confirm('Are you sure?')">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $questions->appends(request()->query())->links() }}
</div>

<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="
        background-color: white; 
        border-radius: 10px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        width: 55%; 
        max-height: 90vh; /* Maximum height to keep it within the viewport */
        margin: auto; 
        padding: 24px; 
        position: relative; 
        overflow-y: auto; /* Make content scrollable if it exceeds max height */
    ">
        <div style="display: flex; justify-content: end; align-items: center; margin-bottom: 16px;">
            <button id="closeModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>

        <form id="question-form" action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Add Question</h1>
            <div class="grid gap-5 grid-cols-5">
                @foreach ($dropdown_list as $moduleName => $module)
                @php
                $id = strtolower(Str::slug($moduleName, '_'));
                $moduleKey = trim(explode('Select', $moduleName)[1]);
                @endphp
                <div class="mb-5">
                    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$moduleName}}</label>
                    <select id="{{ $id }}" name="module[{{ $moduleKey }}][]" class="{{ $id }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required-field">
                        <option value="">{{$moduleName}}</option>
                        @foreach($module as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                    <div class="text-red-500 text-xs mt-1 validation-msg"></div>
                    @error('module.' . $moduleKey)
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach
            </div>

            <div class="relative overflow-x-auto sm:rounded-lg">
                <div id="input-rows"></div>
                <div class="flex justify-end gap-x-5 mt-5">
                    <button type="button" id="add-row" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add New</button>
                    <button type="submit" id="save-btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(count($questions))
<div id="editModal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="
        background-color: white; 
        border-radius: 10px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        width: 55%; 
        max-height: 90vh; /* Maximum height to keep it within the viewport */
        margin: auto; 
        padding: 24px; 
        position: relative; 
        overflow-y: auto; /* Make content scrollable if it exceeds max height */
    ">
        <div style="display: flex; justify-content: end; align-items: center; margin-bottom: 16px;">
            <button id="closeEditModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>
        <form id="questioneditForm" action="{{ route('question.update',  $question->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Edit Question</h1>
            <div id="modalContent">
                <!-- Fields will be dynamically injected here -->
            </div>

            <div class="relative overflow-x-auto sm:rounded-lg">
                <div id="input-rows"></div>
                <div class="flex justify-end gap-x-5 mt-5">
                    <button type="submit" id="Update-btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const editQuestionRoute = "{{ route('question.edit', ':id') }}"; // Define the route here
        var quillQuestion = '';

        document.querySelectorAll('.open-edit-modal').forEach(button => {
            button.addEventListener('click', function() {
                const questionId = this.dataset.id;
                // Fetch data from the server
                const fetchUrl = editQuestionRoute.replace(':id', questionId);

                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate modal with data
                        console.log("edit data question data", data);
                        const modalContent = document.getElementById('modalContent');

                        // Clear previous content
                        modalContent.innerHTML = '';

                        // Populate question ID field (if needed)
                        const questionIdField = document.createElement('input');
                        questionIdField.type = 'hidden';
                        questionIdField.name = 'id';
                        questionIdField.value = data.question.id;
                        modalContent.appendChild(questionIdField);

                        // Populate dropdowns
                        for (const [moduleName, module] of Object.entries(data.dropdown_list)) {
                            const idMapping = {
                                'Select Language': 'language_id',
                                'Select Category': 'category_id',
                                'Select Sub Category': 'sub_category_id',
                                'Select Subject': 'subject_id',
                                'Select Topic': 'topic_id',
                            };

                            // Map the moduleName to the corresponding key in data.question
                            const questionKey = idMapping[moduleName];
                            const selectedId = data.question[questionKey];

                            const id = moduleName.toLowerCase().replace(/\s+/g, '_');

                            const selectField = document.createElement('div');
                            selectField.className = 'mb-4';
                            selectField.innerHTML = `
                                <label for="${id}" class="block text-sm font-medium">${moduleName}</label>
                                <select id="${id}" name="module[${id}][]" class="${id} block w-full p-2 border rounded required" >
                                    <option value="">${moduleName}</option>
                                    ${module.map(item => `
                                        <option value="${item.id}" ${item.id === selectedId ? 'selected' : ''}>${item.name}</option>
                                    `).join('')}
                                </select>
                            `;
                            modalContent.appendChild(selectField);
                        }
                        const questionNumberField = document.createElement('div');
                        questionNumberField.className = 'mb-4';
                        questionNumberField.innerHTML = `
                            <label for="qno" class="block text-sm font-medium">Question No.</label>
                            <input id="qno" type="text" name="qno[]" class="block w-full p-2 border rounded required"
                                value="${data.question.question_number || ''}" placeholder="Question No." />
                        `;
                        modalContent.appendChild(questionNumberField);

                        // Add Photo Link
                        const photoLinkField = document.createElement('div');
                        photoLinkField.className = 'mb-4';
                        photoLinkField.innerHTML = `
                            <label for="photo_link" class="block text-sm font-medium">Photo Link</label>
                            <input id="photo_link" type="text" name="photo_link" class="block w-full p-2 border rounded"
                                value="${data.question.photo_link || ''}" placeholder="Photo Link" />
                        `;
                        modalContent.appendChild(photoLinkField);

                        // Function to dynamically create the photo upload field
                        const uploadPhotoField = document.createElement('div');
                        uploadPhotoField.className = 'flex justify-between w-full mb-2';
                        uploadPhotoField.innerHTML = `
                            <div class='relative'>
                                <label for="fileInput-new" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Photo</label>
                                <button class="remove-image" style='background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;position: absolute;top: 20px;left: 110px;z-index:10;'>X</button>
                                <input type="hidden" id="photo-${data.question.id}" name="photo" value="${data.question.photo}" />
                                <input type="file" accept="image/*" name="photo" style="height: 155px;" class="file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer fileInput" id="fileInput${data.question.id}" />
                                <div class="image-container" style="height: 155px;">
                                    <img id="imagePreview${data.question.id}" class="h-full object-cover rounded-lg imagepreview"
                                        src="${data.question.photo ? '/storage/questions/'+data.question.photo : '/dummy.jpg'}" alt="Image Preview" width="150" />
                                </div>
                                <button type="button" id="fileButton${data.question.id}" class="absolute top-[0px] z-[-1] custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    Upload Photo
                                </button>
                            </div>
                        `;

                        // Append the field to the modal content
                        modalContent.appendChild(uploadPhotoField);

                        const questionField = document.createElement('div');
                        questionField.className = 'mb-4';
                        questionField.style.height = '200px';
                        questionField.innerHTML = `
                            <label for="questionText" class="block text-sm font-medium">Question</label>
                            <div id="questionText" class="editor-question required bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 h-50">
                            ${data.question?.question || ''}
                            </div>
                            <input type="hidden" name="question[]" class="question-input-edit required" value="${data.question?.question || ''}" />
                        `;
                        modalContent.appendChild(questionField);

                        quillQuestion = new Quill('#questionText', {
                            theme: 'snow'
                        });

                        $('#Update-btn').on('click', function() {
                            $('.question-input-edit').val(quillQuestion.root.innerHTML);
                        });

                        const optionsContainer = document.createElement('div');
                        optionsContainer.className = 'grid grid-cols-2 gap-4';
                        ['A', 'B', 'C', 'D'].forEach(option => {
                            optionsContainer.innerHTML += `
                            <div>
                                <label for="option_${option}" class="block text-sm font-medium">Option ${option}</label>
                                <input id="option_${option}" name="option_${option.toLowerCase()}" type="text"
                                    class="block w-full p-2 border rounded required"
                                    value="${data.question[`option_${option.toLowerCase()}`] || ''}">
                            </div>
                        `;
                        });
                        modalContent.appendChild(optionsContainer);

                        const answerField = document.createElement('div');
                        answerField.className = 'mb-4';
                        answerField.innerHTML = `
                            <label for="answer" class="block text-sm font-medium">Answer</label>
                            <select id="answer" name="answer" class="block w-full p-2 border rounded required">
                                <option value="">Select Answer</option>
                                ${['A', 'B', 'C', 'D'].map(option => `
                                    <option value="${option}" ${data.question?.answer === option ? 'selected' : ''}>${option}</option>
                                `).join('')}
                            </select>
                        `;
                        modalContent.appendChild(answerField);


                        // Add the "Level" dropdown
                        const levelField = document.createElement('div');
                        levelField.className = 'mb-4';
                        levelField.innerHTML = `
                        <label for="level" class="block text-sm font-medium">Level</label>
                        <select class='block w-full p-2 border rounded' name='level'>
                        <option>Select</option>
                        <option value=1 ${data.question?.level === '1' ? 'selected' : ''}>Easy</option>
                        <option value=2 ${data.question?.level === '2' ? 'selected' : ''}>Medium</option>
                        <option value=3 ${data.question?.level === '3' ? 'selected' : ''}>Hard</option>
                        </select>
                        `;
                        modalContent.appendChild(levelField);


                        // Add the "Notes" textarea
                        const notesField = document.createElement('div');
                        notesField.className = 'w-full';
                        notesField.innerHTML = `
                            <label for="notes" class="block text-sm font-medium">Notes</label>
                            <textarea id="notes" name="notes[]" rows="3" class="block w-full p-2 border rounded" placeholder="Notes">${data.question.notes??''}</textarea>
                        `;
                        modalContent.appendChild(notesField);
                        document.getElementById('editModal').style.display = 'flex';
                    })
                    .catch(error => console.error('Error fetching question data:', error));
            });

        });

        document.getElementById('questioneditForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const actionUrl = this.action;
            const requiredFields = this.querySelectorAll('.required');
            let allFieldsFilled = true;
            let missingFields = [];

            // Validate required fields
            requiredFields.forEach(field => {
                if (['INPUT', 'TEXTAREA', 'SELECT'].includes(field.tagName)) {
                    // If it's an input, textarea, or select, check its value
                    if (!field.value.trim()) {
                        allFieldsFilled = false;
                        missingFields.push(field.name || field.id || 'Unnamed field');
                    }
                }
            });

            let content = quillQuestion.getText().trim();
            if (content.length === 0) {
                alert('Question field is required.');
                e.preventDefault(); // Prevent form submission
                return;
            }

            if (!allFieldsFilled) {
                alert(`Please fill in all required fields: ${missingFields.join(', ')}`);
                return;
            }

            // for (let [key, value] of formData.entries()) {
            //     console.log(`${key}: ${value}`);
            // }

            setTimeout(() => {
                fetch(actionUrl, {
                        method: this.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            // Handle validation errors if any
                            console.error(data.errors);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 500)
        });

        $(document).on('change', `.file-input`, function(event) {
            const inputId = $(this).attr('id');

            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(`#imagePreview${inputId.replace('fileInput', '')}`).attr('src', e.target.result);
                    $(`#photo-${inputId.replace('fileInput', '')}`).val(file.name); // Update hidden input
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('closeEditModal').addEventListener('click', function() {
            document.getElementById('editModal').style.display = 'none';
        });

        document.getElementById('createButton').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'flex';
        });

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none';
        });

        // SweetAlert2 export button logic
        $('#exportButton').click(function() {
            let languageCheckBoxes = "";
            @foreach($languages as $language)
            languageCheckBoxes += `
            <div id="languageSelectContainer">
                    <div class="flex gap-x-5 items-center">
            
                          <input type="checkbox" id="language_{{ $language->id }}" value="{{ $language->id }}">
                        <label for="language_{{ $language->id }}">{{ $language->name }}</label>
        
                    </div>
                </div>`;
            @endforeach
            Swal.fire({
                title: 'Select Languages for Export',
                html: languageCheckBoxes,
                showCancelButton: true,
                confirmButtonText: 'Export',
                preConfirm: () => {
                    const selectedLanguages = [];
                    $('#languageSelectContainer input[type="checkbox"]:checked').each(function() {
                        selectedLanguages.push($(this).val());
                    });

                    if (selectedLanguages.length === 0) {
                        Swal.showValidationMessage("Please Select at least one language !");
                        return false;
                    }

                    if (selectedLanguages.length > 1) {
                        return Swal.fire({
                            title: 'Multiple Languages Selected',
                            text: 'The export will include questions and options in all selected languages. Do you want to proceed?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, export it!',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                return selectedLanguages;
                            } else {
                                return false;
                            }
                        });
                    } else {
                        return selectedLanguages;
                    }
                }
            }).then((result) => {
                if (result.value) {
                    var form = $('<form>', {
                        'method': 'GET',
                        'action': '{{ route("questions.export") }}'
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));

                    result.value.forEach(function(language) {
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': 'languages[]',
                            'value': language
                        }));

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'language_id',
                                'value': $('#select_language_select').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'category_id',
                                'value': $('#select_category_select').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'sub_category_id',
                                'value': $('#select_sub_category_select').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'subject_id',
                                'value': $('#select_subject_select').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'topic_id',
                                'value': $('#select_topic_select').val()
                            })
                        );
                    });

                    form.appendTo('body').submit();
                }
            });
        });

        //import button logic
        $('#importButton').click(function() {
            //click in file select and save the file in hidden input
            $('#importInput').click();

            // when file is selected, create the form and submit 
            $('#importInput').change(function() {
                var form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ route("questions.import") }}',
                    'enctype': 'multipart/form-data'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': '{{ csrf_token() }}'
                }));

                form.append($(this));

                form.appendTo('body').submit();
            });
        });

        $("#per_page").change(function() {
            $("#page").submit();
        });

        $("#search").click(function() {
            $("#page").submit();
        });

        // Select all checkboxes
        $('.select-all').click(function() {
            $('.select-item').prop('checked', this.checked);
        });

        // Uncheck "select-all" checkbox if any individual checkbox is unchecked
        $('.select-item').change(function() {
            if (!this.checked) {
                $('.select-all').prop('checked', false);
            }

            // Check "select-all" checkbox if all individual checkboxes are checked
            if ($('.select-item:checked').length == $('.select-item').length) {
                $('.select-all').prop('checked', true);
            }
        });

        // Handle the "Delete Selected" button click event
        $('#delete-selected').click(function() {
            let selectedIds = [];

            // Collect all checked items' IDs
            $('.select-item:checked').each(function() {
                selectedIds.push($(this).val());
            });

            // Check if any checkbox is selected
            if (selectedIds.length === 0) {
                alert('Please select at least one question to delete.');
                return;
            }

            // Confirm delete action
            if (!confirm('Are you sure you want to delete the selected questions?')) {
                return;
            }

            $.ajax({
                url: "{{ route('question.bulkDelete') }}", // Define your route for bulk delete
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                    ids: selectedIds
                },
                success: function(response) {
                    location.reload(); // Reload page after successful deletion
                },
                error: function(xhr, status, error) {
                    // Handle error
                    alert('An error occurred while deleting the questions.');
                }
            });
        });

        var languageId = $('#select_language').val();
        var categoryId = $('#select_category').val();
        var subCategoryId = $('#select_sub_category').val();
        var subjectId = $('#select_subject').val();
        var topicId = $('#select_topic').val();

        function fetchQuestions() {
            languageId = $('#select_language').val();
            categoryId = $('#select_category').val();
            subCategoryId = $('#select_sub_category').val();
            subjectId = $('#select_subject').val();
            topicId = $('#select_topic').val();

            if (categoryId && subCategoryId || subjectId || topicId) {
                $('#input-rows').empty();
                var newRow = `
                    <div class="input-row pb-5 border-b-2">   
                        <div class="flex flex-col gap-4 items-center w-full">
        
                            <!-- Question Number -->
                            <div class="w-full flex gap-2">
                                <div class="w-[50%]">
                                    <label for="qno" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question No.</label>
                                    <input id="qno" type="text" class="required w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="qno"
                                        placeholder="Question No."/>
                                </div>
                                <div class="w-[50%]">
                                    <label for="photo_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Photo Link</label>
                                    <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="photo_link"
                                        placeholder="Photo link"/>
                                </div>
                            </div>
        
                            <!-- Photo Link and Upload -->
                            <div class="flex justify-between w-full mb-2">
                                <div class='relative'>
                                    <label for="fileInput-new" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Photo</label>
                                    <button class="remove-image" style='background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;position: absolute;top: 20px;left: 78px;z-index:10;'>X</button>
                                    <input type="hidden" id="photo-new" name="photo" value="" />
                                    <input type="file" accept="image/*" name="photo" style="height: 155px;" class="file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput-new" />
                                    <div class="image-container" >
                                        <img id="imagePreview14" class="h-full object-cover rounded-lg imagepreview" src="/dummy.jpg" alt="Image Preview" width="150" style="width: 100px;">
                                    </div>
                                </div>
                            </div>
        
                            <div class="w-full mt-4">
                                <label for="editor-question" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question</label>
                                <div id="editor-question" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="question[]"></div>
                                <input type="hidden" name="question[]" class="question-input required" />
                            </div>
        
                            <!-- Options A-D -->
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div>
                                    <label for="option_a" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Option A</label>
                                    <input type="text" id="option_a" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required" 
                                        name="option_a[]" placeholder="Option A"  />
                                </div>
                                <div>
                                    <label for="option_b" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Option B</label>
                                    <input type="text" id="option_b" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required" 
                                        name="option_b[]" placeholder="Option B"  />
                                </div>
                                <div>
                                    <label for="option_c" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Option C</label>
                                    <input type="text" id="option_c" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required" 
                                        name="option_c[]" placeholder="Option C"  />
                                </div>
                                <div>
                                    <label for="option_d" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Option D</label>
                                    <input type="text" id="option_d" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required" 
                                        name="option_d[]" placeholder="Option D"  />
                                </div>
                            </div>
        
                            <!-- Answer and Level -->
                            <div class="flex justify-between w-full">
                                <div class="w-[48%]">
                                    <label for="answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Answer</label>
                                    <select id="answer" class="required bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="answer">
                                        <option value="">Select Answer</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
        
                                <div class="w-[48%]">
                                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Level</label>
                                    <select id="level" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="level">
                                        <option value="">Select Level</option>
                                        <option value="1">Easy</option>
                                        <option value="2">Medium</option>
                                        <option value="3">Hard</option>
                                    </select>
                                </div>
                            </div>
        
                            <!-- Notes -->
                            <div class="w-full mt-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                <textarea id="notes" class="w-full required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="notes[]" placeholder="Notes" rows="3"></textarea>
                            </div>
        
                        </div>
        
                        <select id="selectlangauge" name="language[]" class="hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Select Language</option>
                            @foreach($languages as $item)
                            <option value="{{$item->id}}" >{{$item->name}}</option>
                            @endforeach
                        </select>
        
                        <div id="languages-container" class="ms-5"></div>
                    </div>
                `;

                $('#input-rows').append(newRow);

                document.getElementById('fileInput-new').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('imagePreview14').src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });

                var quillQuestion = new Quill('#editor-question', {
                    theme: 'snow'
                });

                $('form').on('submit', function() {
                    // Set the hidden input's value to the content of the Quill editor (HTML)
                    $('.question-input').val(quillQuestion.root.innerHTML);
                });

                attachFileInputHandlers();
            }
        }

        $(document).on('click', '.remove-image', function(event) {
            event.preventDefault(); // Prevent default button behavior

            $('input[name=photo]').val('');

            $('.imagepreview').attr('src', '/dummy.jpg');
        });

        fetchQuestions();

        function attachFileInputHandlers() {
            // Attach event listener to each file input
            $('.file-input').each(function() {
                var fileInput = $(this);
                var questionId = fileInput.attr('id').replace('fileInput-', '');
                var photoInput = $('#photo-' + questionId);
                var fileButton = $('#fileButton-' + questionId);


                fileInput.on('change', function(event) {
                    console.log(fileInput, questionId, photoInput, fileButton);
                    var file = event.target.files[0];
                    if (file) {
                        // Get the file name and truncate it to 20 characters
                        var fileName = file.name.substring(0, 20) + (file.name.length > 20 ? '...' : '');
                        // Update the button text with the file name
                        fileButton.text(fileName);
                        // Update the hidden input value with the file name
                        photoInput.val(fileName);
                    }
                });

                // Trigger file input click when button is clicked
                fileButton.on('click', function() {
                    fileInput.click();
                });
            });
        }

        $('#select_topic').change(function() {
            $.ajax({
                url: "{{ route('get.question_no') }}",
                method: 'GET',
                data: {
                    "category_id": $('#select_category').val(),
                    "sub_category_id": $('#select_sub_category').val(),
                    "subject_id": $('#select_subject').val(),
                    "topic_id": $('#select_topic').val(),
                },
                success: function(data) {
                    $("input[name=qno]").val(Number(data) + 1);
                }
            });

            fetchQuestions();
        });

        $('#add-row').click(function() {
            var isValid = true;

            if (categoryId && subCategoryId || subjectId || topicId) {
                // Show confirmation alert
                if (confirm('Do you want to save the current question and create another one?')) {
                    // Submit the form
                    $('#question-form').submit();

                    // Reload the page after form submission
                    $('#your-form-id').on('submit', function(e) {
                        e.preventDefault(); // Prevent the default form submission
                        $.ajax({
                            type: $(this).attr('method'),
                            url: $(this).attr('action'),
                            data: $(this).serialize(),
                            success: function(response) {
                                // Reload the page after successful form submission
                                location.reload();
                            },
                            error: function(response) {
                                // Handle error
                                alert('An error occurred while saving the question.');
                            }
                        });
                    });
                } else {
                    // If the user cancels, do nothing
                    return;
                }
            } else {
                alert('Please select a category to add a question');
            }
        });

        attachFileInputHandlers();

        $('.file-input').each(function() {
            var fileInput = $(this);
            var questionId = fileInput.attr('id').replace('fileInput', '');
            var photoInput = $('#photo-' + questionId);
            var fileButton = $('#fileButton' + questionId);

            fileInput.on('change', function(event) {
                var file = event.target.files[0];
                if (file) {
                    // Get the file name and truncate it to 5 characters
                    var fileName = file.name.substring(0, 20) + (file.name.length > 5 ? '...' : '');
                    // Update the button text with the file name
                    fileButton.text(fileName);
                    // Update the hidden input value with the file name
                    photoInput.val(fileName);
                }
            });

            // Trigger file input click when button is clicked
            fileButton.on('click', function() {
                fileInput.click();
            });
        });

        // Start Create Question Form
        $('#save-btn').click(function(e) {
            e.preventDefault(); // Prevent the form from submitting immediately
            var isValid = true;

            if (categoryId && subCategoryId || subjectId || topicId) {
                $('#question-form').submit();
            } else {
                alert('Please select a category to add a question');
            }
        });

        $('#question-form').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            const requiredFields = this.querySelectorAll('.required');
            let allFieldsFilled = true;
            let missingFields = [];

            // Validate required fields
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    allFieldsFilled = false;
                    missingFields.push(field.name.replace('[]', '') || field.id || 'Unnamed field');
                }
            });

            if (!allFieldsFilled) {
                alert(`Please fill in all required fields: ${missingFields.join(', ')}`);
                return;
            }

            setTimeout(() => {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'), // Form action URL
                    data: new FormData(this), // Send the form data
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Display success message
                        alert(response.success); // You can replace this with a better UI notification
                        // Reload the page or reset the form if necessary
                        location.reload(); // Optionally reload the page
                    },
                    error: function(xhr) {
                        // Handle errors
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        for (let field in errors) {
                            errorMessage += errors[field].join(' ') + '\n';
                        }
                        alert(errorMessage || 'An error occurred. Please try again.');
                    }
                });
            }, 1000)
        });
        // End Create Question Form

        // Start Load saved settings from localStorage
        const savedColumns = localStorage.getItem('selectedColumns');
        const selectedColumns = savedColumns ? JSON.parse(savedColumns) : [];

        $('#columnSelectToggle').click(function() {
            $('#columnSelectDropdown').toggle();
        });

        // Set the initial state of checkboxes and columns
        $('#columnSelectDropdown input[type="checkbox"]').each(function() {
            const value = $(this).val();
            if ($.inArray(value, selectedColumns) !== -1) {
                $(this).prop('checked', true);
                $(`[data-column="${value}"]`).show();
            } else {
                $(this).prop('checked', false);
                $(`[data-column="${value}"]`).hide();
            }
        });

        // Handle checkbox changes
        $('#columnSelectDropdown input[type="checkbox"]').change(function() {
            const selectedOptions = [];

            // Show/hide columns based on checked checkboxes
            $('#columnSelectDropdown input[type="checkbox"]').each(function() {
                const value = $(this).val();
                if ($(this).is(':checked')) {
                    selectedOptions.push(value);
                    $(`[data-column="${value}"]`).show();
                } else {
                    $(`[data-column="${value}"]`).hide();
                }
            });

            // Save the selected columns to localStorage
            localStorage.setItem('selectedColumns', JSON.stringify(selectedOptions));
        });

        // Close dropdown when clicking outside of it
        $(document).click(function(event) {
            if (!$(event.target).closest('#columnSelectToggle, #columnSelectDropdown').length) {
                $('#columnSelectDropdown').hide();
            }
        });
        // End Load saved settings from localStorage
    });
</script>

@include('script')

@endpush