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
        <div class="flex justify-end">
            <div></div>
            <div></div>
            <a href="{{ route('question.create') }}" type="button" class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</a>
        </div>

        <div class="flex items-end gap-2">
            <select class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer">
                <option>--Select Category--</option>
            </select>

            <select class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer">
                <option>--Select Sub Category--</option>
            </select>

            <div class="relative inline-block w-full text-gray-700 mt-4">
                <div id="columnSelectToggle" class="block px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none cursor-pointer">
                    Select Columns
                </div>
                <div id="columnSelectDropdown" class="absolute z-10 hidden w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
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
            @foreach($questions as $question)
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
                <td class="p-2 flex gap-4" data-column="action">
                    <a href="{{ route('question.edit', $question->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('question.destroy', $question->id) }}" method='POST'>
                        @csrf
                        @method('DELETE')
                        <button href="#" class="font-medium text-danger dark:text-danger-500 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
    });
</script>
@endpush