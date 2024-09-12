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

@section('content')

{{-- @php dd($questionBank) @endphp  --}}

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Question Bank</h1>
    <a href="{{ route('question.create') }}" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</a>

{{-- 
    @foreach ($dropdown_list as $moduleName => $module)
        @php $id = strtolower(Str::slug($moduleName, '_')); @endphp
        <div class="mb-5">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$moduleName}}</label>
                <select id="{{ $id }}" name="module[{{trim(explode('Select', $moduleName)[1])}}][]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">{{$moduleName}}</option>
                @foreach($module as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>
        </div>
    @endforeach --}}
</div>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5">


    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    #
                </th>
                <th scope="col" class="px-6 py-3">
                    Language
                </th>
                <th scope="col" class="px-6 py-3">
                    Image
                </th>
                <th scope="col" class="px-6 py-3">
                    Question
                </th>
                <th scope="col" class="px-6 py-3">
                    Option A
                </th>
                <th scope="col" class="px-6 py-3">
                    Option B
                </th>
                <th scope="col" class="px-6 py-3">
                    Option C
                </th>
                <th scope="col" class="px-6 py-3">
                    Option D
                </th>
                <th scope="col" class="px-6 py-3">
                    Level
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $question)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->id}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->question_bank->language->name ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->photo ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->question ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->option_a ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->option_b ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->option_c ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->option_d ?? ''}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$question->level ?? ''}}
                </th>

                <td class="px-6 py-4 flex gap-4">
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
{{-- 
    <div class="flex justify-end gap-x-5 mt-5">
        <button type="button" id="edit-button" onclick="window.location.href='{{route('question.edit', '1')}}'" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Edit</button>
    </div> --}}
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function fetchQuestions() {
        var languageId = $('#select_language').val();
        var categoryId = $('#select_category').val();
        var subCategoryId = $('#select_sub_category').val();
        var subjectId = $('#select_subject').val();
        var topicId = $('#select_topic').val();

        console.log()

        $.ajax({
            url: '/get-questions',
            method: 'GET',
            data: {
                language_id: languageId,
                category_id: categoryId,
                sub_category_id: subCategoryId,
                subject_id: subjectId,
                topic_id: topicId
            },
            success: function(data) {
                $('#input-rows').empty();
                data.forEach(function(question) {
                    var editUrl = '{{ route("question.edit", ":id") }}'.replace(':id', question.id);
                    var newRow = `
                        <div class="grid grid-cols-8 text-center gap-x-10 input-row mt-5">
                            <input type="hidden" name="id[]" value="${question.id}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="${question.id}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="question[]" value="${question.question}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_a[]" value="${question.option_a}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_b[]" value="${question.option_b}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_c[]" value="${question.option_c}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_d[]" value="${question.option_d}" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="answer[]" value="${question.answer}" />

                            <div class="flex gap-x-5 items-center">

                                <a href="${editUrl}"><label id="edit-button-${question.id}" class="edit-question underline cursor-pointer text-blue-700 hover:text-blue-800">Edit</label></a>
                                <label id="remove-question-${question.id}" class="remove-question underline cursor-pointer text-red-700 hover:text-red-800">Remove</label>

                            </div>
                        </div>
                    `;
                    $('#input-rows').append(newRow);
                });
            }
        });
    }


    $('#select_language').change(function() {
        var languageId = $(this).val();
        $.ajax({
            url: '/get-categories/' + languageId,
            method: 'GET',
            success: function(data) {
                $('#select_category').empty().append('<option value="">Select Category</option>');
                $.each(data, function(key, value) {
                    $('#select_category').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
                fetchQuestions();
            }
        });
    });

    $('#select_category').change(function() {
        var categoryId = $(this).val();
        $.ajax({
            url: '/get-subcategories/' + categoryId,
            method: 'GET',
            success: function(data) {
                $('#select_sub_category').empty().append('<option value="">Select Sub Category</option>');
                $.each(data, function(key, value) {
                    $('#select_sub_category').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
                fetchQuestions();
            }
        });
    });

    $('#select_sub_category').change(function() {
        var subCategoryId = $(this).val();
        $.ajax({
            url: '/get-subjects/' + subCategoryId,
            method: 'GET',
            success: function(data) {
                $('#select_subject').empty().append('<option value="">Select Subject</option>');
                $.each(data, function(key, value) {
                    $('#select_subject').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
                fetchQuestions();
            }
        });
    });

    $('#select_subject').change(function() {
        var subjectId = $(this).val();
        $.ajax({
            url: '/get-topics/' + subjectId,
            method: 'GET',
            success: function(data) {
                $('#select_topic').empty().append('<option value="">Select Topic</option>');
                $.each(data, function(key, value) {
                    $('#select_topic').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
                fetchQuestions();
            }
        });
    });

    $('#select_topic').change(function() {
        fetchQuestions();
    });

    $('#add-row').click(function() {
        var newRow = $('.input-row:first').clone(); // Clone the first row
        newRow.find('input').val(''); // Clear the values in the cloned row
        newRow.find('input[name="id[]"]').prop('disabled', false); // Ensure ID is enabled for new rows
        $('#input-rows').append(newRow); // Append the new row
    });

    $(document).on('click', '.remove-question', function(event) {
        console.log(this);
        event.preventDefault();
        var id = this.id.split('-')[2];
        
        if(id) { 
            // If the row is associated with a question ID (existing question)
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $.ajax({
                url: '/question/' + id + '/delete',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(data) {
                    $('#remove-question-' + id).closest('.input-row').remove();
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        } else {
            // If the row is newly added (no ID), just remove it
            $(this).closest('.input-row').remove();
        }
    });

    $('#edit-button').click(function() {
        var id = $('#input-rows').find('input[name="id[]"]').val();
        if(id) {
            window.location.href = '/question-bank/' + id + '/edit';
        } else {
            alert('Please select a question to edit');
        }
    });
});
</script>
@endsection