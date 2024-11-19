@extends('layouts.app')
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
                    <!-- <input type="hidden" value="{{ $module[$moduleKey] ?? '' }}" name="{{ $moduleKey }}" /> -->
                    <select id="{{ $id }}" name="{{ $moduleKey }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required-field">
                        <option value="">{{$moduleName}}</option>
                        @foreach($module as $item)
                        <option value="{{$item->id}}" {{ $selectedValue == $item->id ?  'selected' : '' }}>{{$item->name}}</option>
                        @endforeach
                    </select>
                    <div class="text-red-500 text-xs mt-1 validation-msg"></div> <!-- Validation Message -->
                    @error('module.' . $moduleKey)
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach

                <button id="filter-btn" type="submit" class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Filter</button>
            </form>
        </div>

        <div class="flex justify-end items-center gap-2">
            <button id="exportButton" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>

            <button id="importButton" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Import</button>

            <input type="file" id="importInput" name="file" class="form-control hidden" required>

            <a href="{{ route('question.create') }}" type="button" class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</a>
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
                            <!-- <div class="justify-start mr-[400px]">
                                <button class="p-2 w-[40px] " id='delete-selected'>
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                                        <path d="M 10 2 L 9 3 L 3 3 L 3 5 L 4.109375 5 L 5.8925781 20.255859 L 5.8925781 20.263672 C 6.023602 21.250335 6.8803207 22 7.875 22 L 16.123047 22 C 17.117726 22 17.974445 21.250322 18.105469 20.263672 L 18.107422 20.255859 L 19.890625 5 L 21 5 L 21 3 L 15 3 L 14 2 L 10 2 z M 6.125 5 L 17.875 5 L 16.123047 20 L 7.875 20 L 6.125 5 z"></path>
                                    </svg>
                                </button>
                            </div> -->
                            <select class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer" name="per_page" id='per_page'>
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
                                <input type="checkbox" value="image" class="mr-2">
                                Image
                            </label>
                            <label class="block">
                                <input type="checkbox" value="link" class="mr-2">
                                Photo Link
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

<div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5">
    <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
                <th scope="col" class="p-2" data-column="link" style="width: 70px;">Img Link</th>
                <th scope="col" class="p-2" data-column="image">Images</th>
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
                <td class="p-2" data-column="image">
                    <a href="{{$question->photo_link ? $question->photo_link : '#'}}" target="_blank">Link</a>
                    <!-- <img src="{{ $question->photo_link ? $question->photo_link : '/dummy.jpg' }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;" /> -->
                </td>
                <!-- <td class="p-2" data-column="image">
                    <img src="{{ $question->photo_link ? $question->photo_link : '/dummy.jpg' }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;" />
                </td> -->
                <td class="p-2" data-column="link">
                    <img src="{{ $question->photo ? '/public/storage/questions/'.$question->photo : '/dummy.jpg' }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;" />
                </td>
                <td class="p-2" data-column="question">{!! $question->question !!}</td>
                <td class="p-2" data-column="optionA">{{ $question->option_a }}</td>
                <td class="p-2" data-column="optionB">{{ $question->option_b }}</td>
                <td class="p-2" data-column="optionC">{{ $question->option_c }}</td>
                <td class="p-2" data-column="optionD">{{ $question->option_d }}</td>
                <td class="p-2" data-column="level">{{ $question->level }}</td>
                <td class="p-2" data-column="notes">{{ $question->notes }}</td>
                <td class="p-2 flex gap-2" data-column="action">
                    <a href="{{ route('question.edit', $question->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('question.destroy', $question->id) }}" method="POST">
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load saved settings from localStorage
        const savedColumns = localStorage.getItem('selectedColumns');
        const selectedColumns = savedColumns ? JSON.parse(savedColumns) : [];

        function generateTableHeaders() {
            const headers = {
                id: 'ID',
                language: 'Language',
                image: 'Image',
                question: 'Question',
                optionA: 'Option A',
                optionB: 'Option B',
                optionC: 'Option C',
                optionD: 'Option D',
                level: 'Level',
                action: 'Action'
            };

            const headerRow = $('#table-headers');
            headerRow.empty();

            selectedColumns.forEach(column => {
                if (headers[column]) {
                    headerRow.append(`<th scope="col" class="p-2" data-column="${column}">${headers[column]}</th>`);
                }
            });
        }

        function toggleColumns() {
            $('#columnSelectDropdown input[type="checkbox"]').each(function() {
                const value = $(this).val();
                if ($(this).is(':checked')) {
                    $(`[data-column="${value}"]`).show();
                } else {
                    $(`[data-column="${value}"]`).hide();
                }
            });
        }

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
                // `
                // <div id="languageSelectContainer">
                //     <div class="flex gap-x-5 items-center">
                //     @foreach($languages as $language)
                //         <input type="checkbox" {{ $language->name == "English" ? 'checked disabled' : ''}} id="language_{{ $language->id }}" value="{{ $language->id }}">
                //         <label for="language_{{ $language->id }}">{{ $language->name }}</label>
                //     @endforeach
                //     </div>
                // </div>`

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
                                'value': $('#select_language').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'category_id',
                                'value': $('#select_category').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'sub_category_id',
                                'value': $('#select_sub_category').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'subject_id',
                                'value': $('#select_subject').val()
                            })
                        );

                        form.append(
                            $('<input>', {
                                'type': 'hidden',
                                'name': 'topic_id',
                                'value': $('#select_topic').val()
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
    });
</script>
@include('script')
@endpush