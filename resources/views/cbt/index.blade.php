@extends('layouts.app')
@php
$dropdown_list = [
'Select Language' => $languages,
//'Select Language-2' => $languages,
'Select Category' => [],
'Select SubCategory' => [],
];
@endphp
@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">CBT</h1>
</div>

<div>
    <form id="main-form" action="{{ route('cbt.deploy') }}" method="POST" enctype="multipart/form-data">
        <div class="flex gap-x-5">
            @foreach($dropdown_list as $key => $value)
            @php
            $id = strtolower(Str::slug($key, '_'));
            @endphp
            <div class="w-1/5">
                <label for="{{ $key }}" class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $key }}</label>
                <select id="{{$id}}" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="{{ trim(explode('Select', $key)[1]) }}" id="{{ $key }}">
                    <option value="">{{ $key }}</option>
                    @foreach($value as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>

        <div class="flex gap-x-5">
            @foreach($dropdown_list as $key => $value)
            @php
            $id = strtolower(Str::slug($key, '_'));
            @endphp
            <div class="w-1/5">
                <label for="{{ $key }}" class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $key }} 2</label>
                <select id="{{$id}}_2" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="{{ trim(explode('Select', $key)[1]) }}_2" id="{{ $key }}_2">
                    <option value="">{{ $key }} 2</option>
                    @foreach($value as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>

        <div id="question-data" class="mt-5"></div>

        <input type="hidden" name="Subject" id="selected_subject">
        <input type="hidden" name="Subject_2" id="selected_subject2">
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const baseUrl = "{{ url('api/cbt') }}";

        $("body").on("click", ".copy_btn", function(e) {
            e.preventDefault();
            let id = $(this).data('index');

            let url = $('#custom_input_' + id).val();

            if (url) {
                navigator.clipboard.writeText(url).then(() => {
                    alert("URL copied to clipboard!");
                }).catch(err => {});
            }
        });

        $('#select_language').change(function() {
            var languageId = $(this).val();
            $('#select_category').empty().append('<option value="">Select Category</option>');
            $('#select_sub_category').empty().append('<option value="">Select Sub Category</option>');

            if (languageId) {
                $.ajax({
                    url: '/get-categories/' + languageId,
                    method: 'GET',
                    success: function(data) {
                        console.log('Categories:', data); // Debugging: Log the data
                        $.each(data, function(key, value) {
                            $('#select_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching categories:', error); // Debugging: Log any errors
                    }
                });
            }
        });

        $('#select_category').change(function() {
            var categoryId = $(this).val();
            $('#select_subcategory').empty().append('<option value="">Select Sub Category</option>');

            if (categoryId) {
                $.ajax({
                    url: '/get-subcategories/' + categoryId,
                    method: 'GET',
                    success: function(data) {
                        console.log('Subcategories:', data); // Debugging: Log the data
                        $.each(data, function(key, value) {
                            $('#select_subcategory').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching subcategories:', error); // Debugging: Log any errors
                    }
                });
            }
        });

        $('#select_subcategory').change(function() {
            var subCategoryId = $(this).val();
            var languageId = $('#select_language').val();
            var categoryId = $('#select_category').val();


            if (subCategoryId) {
                $.ajax({
                    url: '/get-questions-data/' + languageId + '/' + categoryId + '/' + subCategoryId + '/' + null + '/' + null + '/' + null,
                    method: 'GET',
                    success: function(data) {
                        var subjects = data.subjects1;
                        var length = subjects.length;
                        var questionTable =
                            `<div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5 my-5">`;

                        subjects.forEach((subject, index) => {
                            questionTable +=
                                ` 
                                <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <tbody>
                                        <tr>
                                            <td class="p-2" data-column="total-questions">${subject.questions}</td>
                                            <td class="p-2 w-[27%]" data-column="subject">
                                                <input type="hidden" value="${subject.id}" id='subject_${index}' />
                                                ${subject.name}
                                            </td>
                                            <td class="p-2 text-right" data-column="questions">
                                                <input id="questions_${index}" data-id="${index}" name="subjects[${subject.id}]" type="text" class="questionno px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600 w-1/2" id="questions" placeholder="Questions"> 
                                            </td>
                                            <td>
                                            <div class="flex items-center gap-2 justify-end">
                                                <input id="custom_input_${index}" type="text" class="px-4 py-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" placeholder="Custom Input">
                                                <button type="button" id="copy_button_${index}" data-index="${index}" class="copy_btn text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
                                            </div>
                                        </td>
                                        </tr>
                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                            <td colspan="5" class="h-[1px] text-center"></td>
                                        </tr>
                            `;
                        });

                        questionTable += '</div>';

                        $('#question-data').html(questionTable);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching subjects:', error); // Debugging: Log any errors
                    }
                });
            }
        });

        $('#select_language_2').change(function() {
            var languageId = $(this).val();
            $('#select_category_2').empty().append('<option value="">Select Category 2</option>');
            $('#select_subcategory_2').empty().append('<option value="">Select Sub Category 2</option>');

            if (languageId) {
                $.ajax({
                    url: '/get-categories/' + languageId,
                    method: 'GET',
                    success: function(data) {
                        console.log('Categories:', data); // Debugging: Log the data
                        $.each(data, function(key, value) {
                            $('#select_category_2').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching categories:', error); // Debugging: Log any errors
                    }
                });
            }
        });

        $('#select_category_2').change(function() {
            var categoryId = $(this).val();
            $('#select_subcategory_2').empty().append('<option value="">Select Sub Category 2</option>');

            if (categoryId) {
                $.ajax({
                    url: '/get-subcategories/' + categoryId,
                    method: 'GET',
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#select_subcategory_2').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching subcategories:', error); // Debugging: Log any errors
                    }
                });
            }
        });

        $('#select_subcategory_2').change(function() {
            var subCategoryId = $('#select_subcategory').val();
            var languageId = $('#select_language').val();
            var categoryId = $('#select_category').val();

            var subCategory2Id = $(this).val();
            var language2Id = $('#select_language_2').val();
            var category2Id = $('#select_category_2').val();

            if (subCategoryId) {
                $.ajax({
                    url: '/get-questions-data/' + languageId + '/' + categoryId + '/' + subCategoryId + '/' + language2Id + '/' + category2Id + '/' + subCategory2Id,
                    method: 'GET',
                    success: function(data) {
                        var subjects1Details = data.subjects1;
                        var subjects2Details = data.subjects2;

                        var length = Math.min(subjects1Details.length, subjects2Details.length);

                        var questionTable = `
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5 my-5 w-full">
                                <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <tbody>
                        `;

                        // Loop through subjects to create rows
                        for (var i = 0; i < length; i++) {
                            var subject1 = subjects1Details[i];
                            var subject2 = subjects2Details[i];

                            questionTable += `
                                <tr>
                                    <td class="p-2" data-column="total-questions">${subject1.questions}</td>
                                    <td class="p-2" data-column="subject">
                                        <input type="hidden" value="${subject1.id}" id='subject_${i}' />
                                        <input type="hidden" value="${subject2.id}" id='subject2_${i}' />
                                        ${subject1.name} | ${subject2.name}
                                    </td>
                                    <td class="p-2" data-column="questions">
                                        <input id="questions_${i}" data-id="${i}" name="subjects[${subject2.id}]" type="text" class="questionno px-4 py-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" placeholder="Questions">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <input id="custom_input_${i}" type="text" class="px-4 py-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" placeholder="Custom Input">
                                            <button id="copy_button_${i}" data-index="${i}" class="copy_btn text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <td colspan="5" class="h-[1px] text-center"></td>
                                </tr>
                            `;
                        }

                        questionTable += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        $('#question-data').html(questionTable);

                        // Add event listener to copy buttons
                        // for (var i = 0; i < length; i++) {
                        //     $(`#copy_button_${i}`).on('click', function() {
                        //         var index = $(this).data('index');
                        //         var inputToCopy = $(`#custom_input_${index}`).val();

                        //         // Copy to clipboard
                        //         navigator.clipboard.writeText(inputToCopy).then(() => {
                        //             alert('Copied: ' + inputToCopy);
                        //         }).catch(err => {
                        //             console.error('Failed to copy text:', err);
                        //         });
                        //     });
                        // }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching subjects:', error);
                    }
                });
            }
        });

        $("body").on('input', '.questionno', function() {
            let dataId = $(this).attr('data-id');

            $("#subject2_" + dataId).val();
            $("#subject_" + dataId).val();

            let url = `${baseUrl}?`; // Start with the base URL and a question mark for query params

            // Dynamically append parameters if they have values
            if ($("#select_language").val()) {
                url += `Language=${$("#select_language").val()}&`;
            }

            if ($("#select_category").val()) {
                url += `Category=${$("#select_category").val()}&`;
            }

            if ($("#select_subcategory").val()) {
                url += `SubCategory=${$("#select_subcategory").val()}&`;
            }

            if ($("#select_language_2").val()) {
                url += `Language_2=${$("#select_language_2").val()}&`;
            }

            if ($("#select_category_2").val()) {
                url += `Category_2=${$("#select_category_2").val()}&`;
            }

            if ($("#select_subcategory_2").val()) {
                url += `SubCategory_2=${$("#select_subcategory_2").val()}&`;
            }

            if ($("#subject2_" + dataId).val()) {
                url += `Subject_2=${$("#subject2_" + dataId).val()}&`;
            }

            if ($("#subject_" + dataId).val()) {
                url += `Subject=${$("#subject_" + dataId).val()}&`;
            }

            if ($(this).val()) {
                url += `Limit=${$(this).val()}&`;
            }

            // Remove trailing '&' if exists
            url = url.replace(/&$/, '');

            $("#custom_input_" + dataId).val(url);
        });
    });
</script>

@endsection