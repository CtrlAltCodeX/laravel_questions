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


<form action="{{ route('question.store') }}" method="POST"  enctype="multipart/form-data">
    @csrf
    <div class="flex justify-between">
        <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Question Bank</h1>

        @foreach ($dropdown_list as $moduleName => $module)
            @php 
                $id = strtolower(Str::slug($moduleName, '_')); 
                $moduleKey = trim(explode('Select', $moduleName)[1]);
            @endphp
            <div class="mb-5">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$moduleName}}</label>
                    <select id="{{ $id }}" name="module[{{ $moduleKey }}][]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">{{$moduleName}}</option>
                    @foreach($module as $item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
                @error('module.' . $moduleKey)
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
        @endforeach

    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5">

        <div class="grid grid-cols-12 text-center gap-x-10">
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Id</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Photo</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">PhotoLink</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Question</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option A</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option B</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option C</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option D</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Answer</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Level</label>
        </div>

        <div id="input-rows"></div>

        <div class="flex justify-end gap-x-5 mt-5">
            <button type="button" id="add-row" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add</button>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
        </div>
    </div>
</form>
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
                if(data.length > 0){

                    data.forEach(function(question) {
                        var newRow = `
                            <div class="grid grid-cols-12 text-center gap-x-10 input-row mt-5">
                                <input type="hidden" name="id[]" value="${question.id}" />
                                <input type="hidden" name="question_bank_id[]" value="${question.question_bank_id}" />
                                <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="${question.id}" />
                                <div class="relative">
                                    <input type="file" accept="image/*" class="file-input" id="fileInput${question.id}" name="photo[]" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;" />
                                    <button type="button" id="fileButton${question.id}" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        ${question.photo ? question.photo.substring(0, 5) + '...' : ''}
                                    </button>
                                </div>
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="photo_link[]" value="${question.photo_link}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="question[]" value="${question.question}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_a[]" value="${question.option_a}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_b[]" value="${question.option_b}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_c[]" value="${question.option_c}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_d[]" value="${question.option_d}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="answer[]" value="${question.answer}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="notes[]" value="${question.notes}" />
                                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="level[]" value="${question.level}" />

                                <button id="remove-question-${question.id}" type="button" class="text-white remove-question bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Remove</button>

                            </div>
                        `;
                        $('#input-rows').append(newRow);
                    });

                    attachFileInputHandlers();

                }else{
                    var newRow = `
                        <div class="grid grid-cols-12 text-center gap-x-10 input-row mt-5">
                            <input type="hidden" name="id[]" value="" />
                            <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            <div class="relative">
                                <input type="file" accept="image/*" class="file-input" name="photo[]" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;" />
                                <button type="button" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></button>
                            </div>
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="photo_link[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="question[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_a[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_b[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_c[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_d[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="answer[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="notes[]" />
                            <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="level[]" />

                            <button type="button" class="remove-question text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Remove</button>

                        </div>

                    `;

                    $('#input-rows').append(newRow);

                    attachFileInputHandlers();
                }
            }
        });
    }

    fetchQuestions();

    function attachFileInputHandlers() {
        // Attach event listener to each file input
        $('.file-input').each(function() {
            var fileInput = $(this);
            var fileButton = fileInput.siblings('.custom-file-button');

            fileInput.on('change', function(event) {
                var file = event.target.files[0];
                if (file) {
                    var fileName = file.name.substring(0, 5) + (file.name.length > 5 ? '...' : '');
                    fileButton.text(fileName);
                    fileInput.val(file.name);
                }
            });

            // Trigger file input click when button is clicked
            fileButton.on('click', function() {
                fileInput.click();
            });
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
        attachFileInputHandlers();
    });

    $(document).on('click', '.remove-question', function(event) {
        event.preventDefault();
        var id = this.id.split('-')[2];
        
        if(id) { 
            // If the row is associated with a question ID (existing question)
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $.ajax({
                url: '/question-bank/' + id + '/delete',
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
            if($('.input-row').length > 1) {
                $(this).closest('.input-row').remove();
            }
        }
    });

    attachFileInputHandlers();

});
</script>
@endsection