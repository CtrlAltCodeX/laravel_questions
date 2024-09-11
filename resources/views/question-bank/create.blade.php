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


<form action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Question Bank</h1>
    <div class="grid gap-5 grid-cols-5">
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
            <!-- <label class="block text-sm font-medium text-gray-900 dark:text-white">Id</label> -->
            <!-- <label class="block text-sm font-medium text-gray-900 dark:text-white">Photo</label> -->
            <!-- <label class="block text-sm font-medium text-gray-900 dark:text-white">PhotoLink</label> -->
            <!-- <label class="block text-sm font-medium text-gray-900 dark:text-white">Question</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option A</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option B</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option C</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Option D</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Answer</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Level</label> -->
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
                url: '{{ route("questions") }}',
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
                    if (data.length > 0) {
                        data.forEach(function(question) {
                            var newRow = `
                                <div class="input-row">
                                    <div class="col-span-1 flex justify-end">
                                        <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                            X
                                        </button>
                                    </div>    
                                    <div class="grid grid-cols-12 gap-4 items-center text-center mt-5">
                                        <input type="hidden" name="id[]" value="" />

                                        <!-- Image Upload -->
                                        <div class="relative col-span-2 text-left">
                                            <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="option_a[]" 
                                                    placeholder="Question No." />
                                            <input type="file" accept="image/*" class="file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" name="photo[]" />
                                            <button type="button" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                Upload Photo
                                            </button>
                                        </div>

                                        <!-- Question Field -->
                                        <div class="col-span-4 text-left">
                                            <textarea class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="question[]" 
                                                    placeholder="Enter your question here" 
                                                    rows="3"></textarea>
                                        </div>

                                        <!-- Options A-D -->
                                        <div class="col-span-4 grid grid-cols-2 gap-2">
                                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_a[]" 
                                                placeholder="Option A" />
                                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_b[]" 
                                                placeholder="Option B" />
                                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_c[]" 
                                                placeholder="Option C" />
                                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_d[]" 
                                                placeholder="Option D" />
                                        </div>

                                        <!-- Answer Field -->
                                        <div class="col-span-2">
                                            <select class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="answer[]">
                                                <option value="">Select Answer</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                            </select>
                                            <input type="number" class="mt-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="level[]" 
                                                placeholder="Level" />
                                        </div>

                                        <!-- Notes and Level -->
                                        <div class="col-span-5 grid gap-2">
                                            <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="notes[]" 
                                                    placeholder="Notes" 
                                                    rows="3" cols="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#input-rows').append(newRow);
                        });

                        attachFileInputHandlers();

                    } else {
                        var newRow = `
                            <div class="input-row">
                                <div class="col-span-1 flex justify-end">
                                    <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                        X
                                    </button>
                                </div>    
                                <div class="grid grid-cols-12 gap-4 items-center text-center mt-5">
                                    <input type="hidden" name="id[]" value="" />

                                    <!-- Image Upload -->
                                    <div class="relative col-span-2 text-left">
                                        <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_a[]" 
                                                placeholder="Question No." />
                                        <input type="file" accept="image/*" class="file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" name="photo[]" />
                                        <button type="button" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            Upload Photo
                                        </button>
                                    </div>

                                    <!-- Question Field -->
                                    <div class="col-span-4 text-left">
                                        <textarea class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="question[]" 
                                                placeholder="Enter your question here" 
                                                rows="3"></textarea>
                                    </div>

                                    <!-- Options A-D -->
                                    <div class="col-span-4 grid grid-cols-2 gap-2">
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="option_a[]" 
                                            placeholder="Option A" />
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="option_b[]" 
                                            placeholder="Option B" />
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="option_c[]" 
                                            placeholder="Option C" />
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="option_d[]" 
                                            placeholder="Option D" />
                                    </div>

                                    <!-- Answer Field -->
                                    <div class="col-span-2">
                                        <select class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="answer[]">
                                            <option value="">Select Answer</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                        <input type="number" class="mt-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="level[]" 
                                            placeholder="Level" />
                                    </div>

                                    <!-- Notes and Level -->
                                    <div class="col-span-5 grid gap-2">
                                        <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="notes[]" 
                                                placeholder="Notes" 
                                                rows="3" cols="3"></textarea>
                                    </div>
                                </div>
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
                        $('#select_category').append('<option value="' + value.id + '">' + value.name + '</option>');
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
                        $('#select_sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
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
                        $('#select_subject').append('<option value="' + value.id + '">' + value.name + '</option>');
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
                        $('#select_topic').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    fetchQuestions();
                }
            });
        });

        $('#select_topic').change(function() {
            fetchQuestions();
        });

        $('#add-row').click(function() {
            var newRow = $('.input-row').clone(); // Clone the first row
            newRow.find('input').val(''); // Clear the values in the cloned row
            newRow.find('input[name="id[]"]').prop('disabled', false); // Ensure ID is enabled for new rows
            $('#input-rows').append(newRow); // Append the new row
            attachFileInputHandlers();
        });

        $(document).on('click', '.remove-question', function(event) {
            event.preventDefault();
            var id = this.id.split('-')[2];

            if (id) {
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
                if ($('.input-row').length > 1) {
                    $(this).closest('.input-row').remove();
                }
            }
        });

        attachFileInputHandlers();
    });
</script>
@endsection