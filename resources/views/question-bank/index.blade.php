@extends('layouts.app')
@php 
    $dropdown_list = [
    'Select Language' => $languages,
    'Select Category' => [],
    'Select Sub Category' => [],
    'Select Subject' => [],
    'Select Topic' => [],
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

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Question</h1>
    <div class="flex flex-col items-self-end">
        <div class="flex justify-end items-center gap-2">
            <form action="{{ route('question.index') }}" method="GET" id='data' class="mb-0 flex gap-2">
                <input type="hidden" value="{{ request()->per_page }}" name="per_page" />
                <input type="hidden" value="{{ request()->search }}" name="search" />

                <select class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer" id='select_category' name="category_id">
                    <option value="">--Select Category--</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request()->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer" id='select_sub_category' name="sub_category_id">
                    <option value="">--Select Sub Category--</option>
                    @foreach($subCategories as $subcategory)
                    <option value="{{ $subcategory->id }}" {{ request()->sub_category_id == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                    @endforeach
                </select>
            </form>

            <form action="{{ route('questions.export') }}" method="GET" class="m-0">
                @csrf
                <input type="hidden" name="category_id" value="{{request()->category_id}}" />
                <input type="hidden" name="sub_category_id" value="{{request()->sub_category_id}}" />
                
                <button type="submit" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>
            </form>

            <form action="{{ route('questions.import') }}" method="POST" class="m-0" enctype="multipart/form-data">
                @csrf
                <div class="form-group hidden">
                    <label for="file">Choose Excel File</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Import</button>
            </form>

            <a href="{{ route('question.create') }}" type="button" class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</a>
        </div>

        <div class="flex flex-col items-end gap-y-5">
            <div class="flex items-end gap-2 mt-2">
                <div class="relative inline-block w-full text-gray-700 mt-4">
                    <form action="{{ route('question.index') }}" method="GET" id='page'>
                        <input type="hidden" name="category_id" value="{{request()->category_id}}" />
                        <input type="hidden" name="sub_category_id" value="{{request()->sub_category_id}}" />

                        <div class="flex gap-2">
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
                                #
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

<div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5 mt-3">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead>
            <tr>
                <th scope="col" class="p-2" data-column="id">#</th>
                <th scope="col" class="p-2" data-column="language">Language</th>
                <th scope="col" class="p-2" data-column="image">Image</th>
                <th scope="col" class="p-2" data-column="question">Question</th>
                <th scope="col" class="p-2" data-column="optionA">Option A</th>
                <th scope="col" class="p-2" data-column="optionB">Option B</th>
                <th scope="col" class="p-2" data-column="optionC">Option C</th>
                <th scope="col" class="p-2" data-column="optionD">Option D</th>
                <th scope="col" class="p-2" data-column="level">Level</th>
                <th scope="col" class="p-2" data-column="action">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($questions as $question)
            <tr>
                <td class="p-2" data-column="id">{{$question->id}}</td>
                <td class="p-2" data-column="language">{{$question->question_bank->language->name ?? ''}}</td>
                <td class="p-2" data-column="image">{{$question->photo ?? ''}}</td>
                <td class="p-2" data-column="question">{{$question->question ?? ''}}</td>
                <td class="p-2" data-column="optionA">{{$question->option_a ?? ''}}</td>
                <td class="p-2" data-column="optionB">{{$question->option_b ?? ''}}</td>
                <td class="p-2" data-column="optionC">{{$question->option_c ?? ''}}</td>
                <td class="p-2" data-column="optionD">{{$question->option_d ?? ''}}</td>
                <td class="p-2" data-column="level">{{$question->level ?? ''}}</td>
                <td class="p-2 flex gap-2" data-column="action">
                    <a href="{{ route('question.edit', $question->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('question.destroy', $question->id) }}" method='POST'>
                        @csrf
                        @method('DELETE')
                        <button href="#" class="font-medium text-danger dark:text-danger-500 hover:underline" onclick="return confirm('Are you sure?')">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr class="text-center">
                <td colspan="10">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $questions->links() }}
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const $toggle = $('#columnSelectToggle');
        const $dropdown = $('#columnSelectDropdown');

        // Toggle dropdown visibility on click
        $toggle.on('click', function() {
            $dropdown.toggleClass('hidden');
        });

        // Close dropdown if clicked outside
        $(document).on('click', function(event) {
            if (!$toggle.is(event.target) && !$toggle.has(event.target).length &&
                !$dropdown.is(event.target) && !$dropdown.has(event.target).length) {
                $dropdown.addClass('hidden');
            }
        });

        // Toggle the dropdown visibility
        $('#columnSelectToggle').click(function() {
            $('#columnSelectDropdown').toggle();
        });

        // Load saved settings from localStorage
        const savedColumns = localStorage.getItem('selectedColumns');
        const selectedColumns = savedColumns ? JSON.parse(savedColumns) : [];

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

        $('#select_category').change(function() {
            $('#select_sub_category').empty();
            $('#select_sub_category').append('<option>--Select Sub Category--</option>');
            var categoryId = $(this).val();

            if (categoryId) {
                $.ajax({
                    url: '/get-subcategories/' + categoryId,
                    method: 'GET',
                    success: function(data) {
                        $('#select_sub_category').empty().append('<option value="">Select Sub Category</option>');

                        $.each(data, function(key, value) {
                            $('#select_sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        $("#per_page").change(function() {
            $("#page").submit();
        });

        $("#search").click(function() {
            $("#page").submit();
        });

        $("#select_sub_category").change(function() {
            $("#data").submit();
        });
    });
</script>
@endpush