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
    <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white flex flex-col justify-between mb-0 items-center">Question
        <button class="p-2 w-[40px] mr-5" id='delete-selected'>
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                <path d="M 10 2 L 9 3 L 3 3 L 3 5 L 4.109375 5 L 5.8925781 20.255859 L 5.8925781 20.263672 C 6.023602 21.250335 6.8803207 22 7.875 22 L 16.123047 22 C 17.117726 22 17.974445 21.250322 18.105469 20.263672 L 18.107422 20.255859 L 19.890625 5 L 21 5 L 21 3 L 15 3 L 14 2 L 10 2 z M 6.125 5 L 17.875 5 L 16.123047 20 L 7.875 20 L 6.125 5 z"></path>
            </svg>
        </button>
    </h1>
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

            <button id="exportButton" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>

            <!-- <form action="{{ route('questions.export') }}" method="GET" class="m-0">
                @csrf
                <input type="hidden" name="category_id" value="{{request()->category_id}}" />
                <input type="hidden" name="sub_category_id" value="{{request()->sub_category_id}}" />

                <button type="submit" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button>
            </form> -->

            <button id="importButton" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Import</button>
            <input type="file" id="importInput" name="file" class="form-control hidden" required>

            <!-- <form action="{{ route('questions.import') }}" method="POST" class="m-0" enctype="multipart/form-data">
                @csrf
                <div class="form-group hidden">
                    <label for="file">Choose Excel File</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="text-center hover:text-white border border-bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Import</button>
            </form> -->

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
                                ID
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

<div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead>
            <tr>
                <th scope="col" class="p-2" data-column="id">
                    <input type="checkbox" class="select-all" />
                </th>
                <th scope="col" class="p-2" data-column="id">ID</th>
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
                <td class="p-2" data-column="id">
                    <input type="checkbox" class="select-item" value="{{$question->id}}" />
                </td>
                <td class="p-2" data-column="id">{{$question->id}}</td>
                <td class="p-2" data-column="language">{{$question->question_bank->language->name ?? $question->language->name}}</td>
                <td class="p-2" data-column="image">{{$question->photo ?? ''}}</td>
                <td class="p-2" data-column="question">{{$question->question ?? ''}}</td>
                <td class="p-2" data-column="optionA">{{$question->option_a ?? ''}}</td>
                <td class="p-2" data-column="optionB">{{$question->option_b ?? ''}}</td>
                <td class="p-2" data-column="optionC">{{$question->option_c ?? ''}}</td>
                <td class="p-2" data-column="optionD">{{$question->option_d ?? ''}}</td>
                <td class="p-2" data-column="level">{{$question->level ?? ''}}</td>
                <td class="p-2 flex gap-2" data-column="action">
                    <a href="{{ route('question.edit', $question->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        <!-- Edit Icon -->
                    </a>
                </td>
            </tr>
            @empty
            <tr class="text-center">
                <td colspan="11">No Result Found</td>
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

        // SweetAlert2 export button logic
        $('#exportButton').click(function() {
            Swal.fire({
                title: 'Select Languages for Export',
                html: `
                <div id="languageSelectContainer">
                    <div class="flex gap-x-5 items-center">
                    @foreach($languages as $language)
                        <input type="checkbox" {{ $language->name == "English" ? 'checked disabled' : ''}} id="language_{{ $language->id }}" value="{{ $language->id }}">
                        <label for="language_{{ $language->id }}">{{ $language->name }}</label>
                    @endforeach
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Export',
                preConfirm: () => {
                    const selectedLanguages = [];
                    $('#languageSelectContainer input[type="checkbox"]:checked').each(function() {
                        selectedLanguages.push($(this).val());
                    });
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
@endpush