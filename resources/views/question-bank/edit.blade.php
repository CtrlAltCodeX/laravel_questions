@extends('layouts.app')
@php 
    $dropdown_list = [
    'Select Language' => $languages,
    'Select Category' => $categories,
    'Select Sub Category' => $subCategories,
    'Select Subject' => $subjects,
    'Select Topic' => $topics,
    ];
@endphp
@section('content')

<form action="{{ route('question.update',  $question->id) }}" method="POST"  enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Question Bank</h1>
    <div class="grid gap-5 grid-cols-5">
        @foreach ($dropdown_list as $moduleName => $module)
            @php 
                $id = strtolower(Str::slug($moduleName, '_')); 
                $moduleKey = trim(explode('Select', $moduleName)[1]);
                $selectedId = null;
                switch ($moduleName) {
                    case 'Select Language':
                        $selectedId = $question->question_bank->language_id;
                        break;
                    case 'Select Category':
                        $selectedId = $question->question_bank->category_id;
                        break;
                    case 'Select Sub Category':
                        $selectedId = $question->question_bank->sub_category_id;
                        break;
                    case 'Select Subject':
                        $selectedId = $question->question_bank->subject_id;
                        break;
                    case 'Select Topic':
                        $selectedId = $question->question_bank->topic_id;
                        break;
                }
            @endphp
            <div class="mb-5">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$moduleName}}</label>
                <select id="{{ $id }}" name="module[{{ $moduleKey }}][]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">{{$moduleName}}</option>
                    @foreach($module as $item)
                        <option value="{{$item->id}}" {{$item->id == $selectedId ? 'selected' : ''}}>{{$item->name}}</option>
                    @endforeach
                </select>
                @error('module.' . $moduleKey)
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5">

            {{-- <div class="grid grid-cols-11 text-center gap-x-10">
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
            </div> --}}

        <div id="input-rows">

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
                                name="qno[]" 
                                placeholder="Question No."/>
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
                                rows="3">{{$question->question}}</textarea>
                    </div>

                    <!-- Options A-D -->
                    <div class="col-span-4 grid grid-cols-2 gap-2">
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="option_a[]" 
                            placeholder="Option A" value="{{$question->option_a}}"/>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="option_b[]" 
                            placeholder="Option B" value="{{$question->option_b}}"/>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="option_c[]" 
                            placeholder="Option C" value="{{$question->option_c}}"/>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="option_d[]" 
                            placeholder="Option D" value="{{$question->option_d}}"/>
                    </div>

                    <!-- Answer Field -->
                    <div class="col-span-2">
                        <select class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                name="answer[]">
                                <option value="">Select Answer</option>
                                @foreach (['A', 'B', 'C', 'D'] as $option)
                                    <option value="{{$option}}" {{$option == explode(' ', $question->answer)[1] ? 'selected' : ''}}>{{$option}}</option>
                                @endforeach
                        </select>
                        <input type="number" class="mt-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="level[]" 
                            placeholder="Level" value={{$question->level}}/>
                    </div>

                    <!-- Notes and Level -->
                    <div class="col-span-5 grid gap-2">
                        <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                name="notes[]" 
                                placeholder="Notes" 
                                rows="3" cols="3">{{$question->notes}}</textarea>
                    </div>
                </div>
            </div>





























            {{-- <div class="grid grid-cols-11 text-center gap-x-10 input-row my-5">
                    
                <input type="hidden" name="id[]" value="{{ $question->id }}" />
                
                <input type="text" disabled class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ $question->id }}" />
                                    
                <div class="relative">
                    <input type="file" accept="image/*" class="file-input" id="fileInput{{ $question->id }}" name="photo[]" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;" />
                    <button type="button" id="fileButton{{ $question->id }}" class="bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        {{ $question->photo ? (strlen($question->photo) > 5 ? substr($question->photo, 0, 5).'...' : $question->photo) : '' }}
                    </button>
                </div>

                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="photo_link[]" value="{{ $question->photo_link }}" />

                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="question[]" value="{{ $question->question }}" />
                
                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_a[]" value="{{ $question->option_a }}" />
                
                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_b[]" value="{{ $question->option_b }}" />
                
                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_c[]" value="{{ $question->option_c }}" />
                
                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="option_d[]" value="{{ $question->option_d }}" />
                
                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="answer[]" value="{{ $question->answer }}" />

                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="notes[]" value="{{ $question->notes }}" />
                
                <input type="text" class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="level[]" value="{{ $question->level }}" />
                
                
            </div> --}}
        </div>
        <div class="flex justify-end gap-x-5">
            {{-- <button id="remove-question-{{$question->id}}" type="button" class="text-white remove-question bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Remove</button> --}}
            {{-- <button type="button" id="add-row" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add</button> --}}
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    var languages = @json($languages);
    var categories = @json($categories);
    var sub_categories = @json($subCategories);
    var subjects = @json($subjects);
    var topics = @json($topics);

    function clearOtherDropDowns(currentDropDownIndex,dropdownList) {
        for (let i = currentDropDownIndex + 1; i < dropdownList.length; i++) {
            $(`#${dropdownList[i]}`).html('<option value="">Select</option>');
        }
    }

    $('#select_language').change(function() {
        var languageId = $(this).val();
        var filteredCategories = categories.filter(function(category) {
            return category.language_id == languageId;
        });

        // Populate the categories dropdown
        $('#select_category').html('<option value="">Select Category</option>');
        filteredCategories.forEach(function(category) {
            $('#select_category').append('<option value="' + category.id + '">' + category.name + '</option>');
        });

    });

    $('#select_category').change(function() {
        var categoryId = $(this).val();
        var filteredSubCategories = sub_categories.filter(function(sub_category) {
            return sub_category.category_id == categoryId;
        });

        // Populate the sub categories dropdown
        $('#select_sub_category').html('<option value="">Select Sub Category</option>');
        filteredSubCategories.forEach(function(sub_category) {
            $('#select_sub_category').append('<option value="' + sub_category.id + '">' + sub_category.name + '</option>');
        });

    });

    $('#select_sub_category').change(function() {
        var subCategoryId = $(this).val();
        var filteredSubjects = subjects.filter(function(subject) {
            return subject.sub_category_id == subCategoryId;
        });

        // Populate the subjects dropdown
        $('#select_subject').html('<option value="">Select Subject</option>');
        filteredSubjects.forEach(function(subject) {
            $('#select_subject').append('<option value="' + subject.id + '">' + subject.name + '</option>');
        });
    });

    $('#select_subject').change(function() {
        var subjectId = $(this).val();
        var filteredTopics = topics.filter(function(topic) {
            return topic.subject_id == subjectId;
        });

        // Populate the topics dropdown
        $('#select_topic').html('<option value="">Select Topic</option>');
        filteredTopics.forEach(function(topic) {
            $('#select_topic').append('<option value="' + topic.id + '">' + topic.name + '</option>');
        });
    });

    $('.file-input').each(function() {
        var fileInput = $(this);
        var questionId = fileInput.attr('id').replace('fileInput', '');
        var fileButton = $('#fileButton' + questionId);

        fileInput.on('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                // Get the file name and truncate it to 5 characters
                var fileName = file.name.substring(0, 5) + (file.name.length > 5 ? '...' : '');
                // Update the button text with the file name
                fileButton.text(fileName);
            }
        });

        // Trigger file input click when button is clicked
        fileButton.on('click', function() {
            fileInput.click();
        });
    });

    $('#add-row').click(function() {
        var newRow = $('.input-row:first').clone(); // Clone the first row
        newRow.find('input').val(''); // Clear the values in the cloned row
        newRow.find('input[name="id[]"]').prop('disabled', false); // Ensure ID is enabled for new rows
        $('#input-rows').append(newRow); // Append the new row
    });

    $(document).on('click', '.remove-question', function(event) {
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
            if($('.input-row').length > 1) {
                $(this).closest('.input-row').remove();
            }
        }
    });
});
</script>
@endsection