@extends('layouts.app')
@php 
    $dropdown_list = [
    //'Select Language' => $languages,
    'Select Category' => $categories,
    'Select Sub Category' => $subCategories,
    'Select Subject' => $subjects,
    'Select Topic' => $topics,
    ];

    $levels = [
        '1' => 'Easy',
        '2' => 'Medium',
        '3' => 'Hard',
    ];
@endphp
@section('content')

<form action="{{ route('question.update',  $question->id) }}" method="POST" enctype="multipart/form-data">
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
        $selectedId = $question->language_id;
        break;
        case 'Select Category':
        $selectedId = $question->category_id;
        break;
        case 'Select Sub Category':
        $selectedId = $question->sub_category_id;
        break;
        case 'Select Subject':
        $selectedId = $question->subject_id;
        break;
        case 'Select Topic':
        $selectedId = $question->topic_id;
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
        <div id="input-rows">

            <div class="input-row">
                <div class="col-span-1 flex justify-end">
                    <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        X
                    </button>
                </div>
                <div class="grid grid-cols-12 gap-4 items-start text-center mt-5">
                    <input type="hidden" name="id" value="{{$question->id}}" />
                    <input type="hidden" name="language_id[]" value="{{$question->language_id}}" />

                    <!-- Image Upload -->
                    <div class="col-span-2">
                        <div class="flex col-2 gap-x-4">
<<<<<<< HEAD
                            <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                name="qno" value="{{$question->question_number}}"
                                placeholder="Question No." />
=======
                            <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="qno" value="{{$question->question_number}}"   
                                        placeholder="Question "/>
>>>>>>> 8faa71a222ba96f2b31eeec9fd581a54b6a34131

                            <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                name="photo_link" value="{{$question->photo_link}}"
                                placeholder="photo link" />
                        </div>
                        <div class="relative col-span-2 text-left">
                            <input type="hidden" id="photo-{{$question->id}}" name="photo" value="{{$question->photo}}" />
                            <input type="file" accept="image/*" name="photo" class="file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput{{ $question->id }}" />

                            <div class="image-container">
                                <img id="imagePreview{{$question->id}}" class="w-full h-full object-cover rounded-lg"
                                    src="{{ $question->photo ? '/'.$question->photo : '/dummy.jpg' }}"
                                    alt="Image Preview" width="100" />
                            </div>

                            <button id="fileButton{{$question->id}}" type="button" class="hidden custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                {{ $question->photo ?? 'Upload Photo' }}
                            </button>
                        </div>
                    </div>

                    <!-- Question Field -->
                    <div class="col-span-4 text-left">
                        <div class='mb-2'>
                            <div class="editor-question required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                name="question[]">
                                {!! $question->question !!}
                            </div>
                            <input type="hidden" name="question[]" class="question-input" />
                            <div class="text-red-500 text-xs validation-msg"></div>
                        </div>
                        <!-- <textarea class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="question[]"
                            placeholder="Enter your question here"
                            rows="3"></textarea> -->
                    </div>

                    <!-- Options A-D -->
                    <div class="col-span-4 grid grid-cols-2 gap-2">
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="option_a[]"
                            placeholder="Option A" value="{{$question->option_a}}" />
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="option_b[]"
                            placeholder="Option B" value="{{$question->option_b}}" />
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="option_c[]"
                            placeholder="Option C" value="{{$question->option_c}}" />
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="option_d[]"
                            placeholder="Option D" value="{{$question->option_d}}" />
                    </div>

                    <!-- Answer Field -->
                    <div class="col-span-2">
                        <select class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="answer">
                            <option value="">Select Answer</option>
                            @foreach (['A', 'B', 'C', 'D'] as $option)
                            <option value="{{$option}}" {{$option == $question->answer ? 'selected' : ''}}>{{$option}}</option>
                            @endforeach
                        </select>
                        <select class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="level">
                            <option value="">Select Level</option>
                            @foreach ($levels as $value => $name)
                            <option value="{{$value}}" {{$value == $question->level ? 'selected' : ''}}>{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notes and Level -->
                    <div class="col-span-5 grid gap-2">
                        <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            name="notes[]"
                            placeholder="Notes"
                            rows="3" cols="3">{{$question->notes}}</textarea>
                    </div>

                    {{-- @php dd($question); @endphp --}}

                    <div class="col-span-3">
                        <select id="selectlangauge" name="language[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Select Language</option>
                            @foreach($languages as $item)
                            <option value="{{$item->id}}" {{$item->id == $question->language_id ? 'selected' : ''}}>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Languages --}}
                <div id="languages-container" class="ms-5">
                    @if(count($translatedQuestions) > 0)
                    @foreach ($translatedQuestions as $translatedQuestion)
                    <div id="language-section-{{$translatedQuestion->language_id}}">
                        <div class="border-t border-gray-300 mt-5"></div>
                        <div class="flex justify-end">
                            <button type="button" class="remove-language text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800" data-translation-question-id="{{ $translatedQuestion->id }}" data-section-id="{{$translatedQuestion->language_id}}">
                                X
                            </button>
                        </div>
                        <div class="grid grid-cols-12 gap-4 items-start text-center mt-5 language-section" id="language-section-{{$translatedQuestion->language_id}}" data-section-id="{{$translatedQuestion->language_id}}">
                            <input type="hidden" name="language_id[]" value="{{$translatedQuestion->language_id}}" />

<<<<<<< HEAD
                            <!-- Image Upload -->
                            <div class="col-span-2">
                                <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="qno[]" disabled
                                    placeholder="Question No." value="{{$translatedQuestion->question->question_number}}" />
=======
                                <!-- Image Upload -->
                                <div class="col-span-2">
                                    <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="qno[]" 
                                            
                                            placeholder="Question No." value="{{$translatedQuestion->question->question_number}}"/>
                                </div>

                                <!-- Question Field -->
                                <div class="col-span-4 text-left">
                                    <textarea class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="question[]" 
                                            placeholder="Enter your question here" 
                                            rows="3">{{$translatedQuestion->question_text}}</textarea>
                                </div>

                                <!-- Options A-D -->
                                <div class="col-span-4 grid grid-cols-2 gap-2">
                                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="option_a[]" 
                                        placeholder="Option A" value="{{$translatedQuestion->option_a}}"/>
                                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        name="option_b[]" 
                                        placeholder="Option B" value="{{$translatedQuestion->option_b}}"/>
                                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        name="option_c[]" 
                                        placeholder="Option C" value="{{$translatedQuestion->option_c}}"/>
                                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        name="option_d[]" 
                                        placeholder="Option D" value="{{$translatedQuestion->option_d}}"/>
                                </div>

                                <!-- Notes and Level -->
                                <div class="col-span-5 grid gap-2">
                                    <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="notes[]" 
                                            placeholder="Notes" 
                                            rows="3" cols="3">{{  $translatedQuestion->notes }}</textarea>

                                </div>

                                <div class="col-span-3">
                                    <select id="selectlangauge${languageCount}" name="language[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select Language</option>
                                        @foreach($languages as $item)
                                            <option value="{{$item->id}}" {{ $translatedQuestion->language_id == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
>>>>>>> 8faa71a222ba96f2b31eeec9fd581a54b6a34131
                            </div>

                            <!-- Question Field -->
                            <div class="col-span-4 text-left">
                                <div class='mb-2'>
                                    <div class="editor-question required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        name="question[]">
                                        {!! $translatedQuestion->question_text !!}
                                    </div>
                                    <input type="hidden" name="question[]" class="question-input" />
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                                <!-- <textarea class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="question[]"
                                    placeholder="Enter your question here"
                                    rows="3">{{$translatedQuestion->question_text}}</textarea> -->
                            </div>

                            <!-- Options A-D -->
                            <div class="col-span-4 grid grid-cols-2 gap-2">
                                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="option_a[]"
                                    placeholder="Option A" value="{{$translatedQuestion->option_a}}" />
                                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="option_b[]"
                                    placeholder="Option B" value="{{$translatedQuestion->option_b}}" />
                                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="option_c[]"
                                    placeholder="Option C" value="{{$translatedQuestion->option_c}}" />
                                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="option_d[]"
                                    placeholder="Option D" value="{{$translatedQuestion->option_d}}" />
                            </div>

                            <!-- Notes and Level -->
                            <div class="col-span-5 grid gap-2">
                                <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    name="notes[]"
                                    placeholder="Notes"
                                    rows="3" cols="3">{{ $translatedQuestion->notes }}</textarea>

                            </div>

                            <div class="col-span-3">
                                <select id="selectlangauge${languageCount}" name="language[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Language</option>
                                    @foreach($languages as $item)
                                    <option value="{{$item->id}}" {{ $translatedQuestion->language_id == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endforeach
                        @endif

                    </div>

                    <div class="flex justify-start gap-x-5">
                        <button type="button" id="addLanguage" class="my-5 text-black border-black border bg-white hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-black font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center ">Add Language</button>
                    </div>

                </div>
            </div>
            <div class="flex justify-end gap-x-5">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
            </div>
        </div>
</form>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        var languages = @json($languages);
        var categories = @json($categories);
        var sub_categories = @json($subCategories);
        var subjects = @json($subjects);
        var topics = @json($topics);

        function clearOtherDropDowns(currentDropDownIndex, dropdownList) {
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

            clearOtherDropDowns(0, ['select_category', 'select_sub_category', 'select_subject', 'select_topic']);

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

            clearOtherDropDowns(1, ['select_sub_category', 'select_subject', 'select_topic']);

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

            clearOtherDropDowns(2, ['select_subject', 'select_topic']);
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

            clearOtherDropDowns(3, ['select_topic']);
        });

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

            $('#fileButton{{ $question->id }}, #imagePreview{{ $question->id }}').on('click', function() {
                $('#fileInput{{ $question->id }}').click();
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

            if (id) {
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
                if ($('.input-row').length > 1) {
                    $(this).closest('.input-row').remove();
                }
            }
        });

        var languageCount = 0;

        $('#addLanguage').click(function() {
            languageCount++;

            var qno = $('input[name="qno[]"]').last().val() || '';
            var question = $('textarea[name="question[]"]').last().val() || '';
            var optionA = $('input[name="option_a[]"]').last().val() || '';
            var optionB = $('input[name="option_b[]"]').last().val() || '';
            var optionC = $('input[name="option_c[]"]').last().val() || '';
            var optionD = $('input[name="option_d[]"]').last().val() || '';
            var answer = $('select[name="answer[]"]').last().val() || '';
            var level = $('input[name="level[]"]').last().val() || '';
            var notes = $('textarea[name="notes[]"]').last().val() || '';
            var language = $('select[name="language[]"]').last().val() || '';

            var newLanguage = `
            <div id="language-section-${languageCount}">
                <div class="border-t border-gray-300 mt-5"></div>
                <div class="flex justify-end">
                    <button type="button" class="remove-language text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800" data-section-id="${languageCount}">
                        X
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-4 items-start text-center mt-5 language-section" id="language-section-${languageCount}">
                <input type="hidden" name="language_id[]" value="${languageCount}" />

                <!-- Image Upload -->
                <div class="col-span-2">
                    <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="qno[]" disabled
                            placeholder="Question No." value="${qno}"/>
                </div>

                <!-- Question Field -->
                <div class="col-span-4 text-left">
                    <div class='mb-2'>
                        <div class="editor-question required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="question[]">
                            ${question}
                        </div>
                        <input type="hidden" name="question[]" class="question-input" />
                        <div class="text-red-500 text-xs validation-msg"></div>
                    </div>
                </div>

                <!-- Options A-D -->
                <div class="col-span-4 grid grid-cols-2 gap-2">
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        name="option_a[]" 
                        placeholder="Option A" value="${optionA}"/>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        name="option_b[]" 
                        placeholder="Option B" value="${optionB}"/>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        name="option_c[]" 
                        placeholder="Option C" value="${optionC}"/>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        name="option_d[]" 
                        placeholder="Option D" value="${optionD}"/>
                </div>

                <!-- Notes and Level -->
                <div class="col-span-5 grid gap-2">
                    <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            name="notes[]" 
                            placeholder="Notes" 
                            rows="3" cols="3">${notes}</textarea>
                </div>

                <div class="col-span-3">
                    <select id="selectlangauge${languageCount}" name="language[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Select Language</option>
                        @foreach($languages as $item)
                            <option value="{{$item->id}}" ${language == '{{$item->id}}' ? 'selected' : ''}>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        `;
            $('#languages-container').append(newLanguage);
            $('#languages-container').removeClass('hidden');
            $('#addLanguage').appendTo('#languages-container');

            var quillQuestion = new Quill('.editor-question', {
                theme: 'snow'
            });

            $('form').on('submit', function() {
                // Set the hidden input's value to the content of the Quill editor (HTML)
                $('.question-input').val(quillQuestion.root.innerHTML);
            });

            // Reinitialize Flowbite accordion
            const accordionItems = document.querySelectorAll('[data-accordion-target]');
            accordionItems.forEach(item => {
                item.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-accordion-target'));
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);
                    target.classList.toggle('hidden');
                });
            });

        });

        // Remove language section
        $(document).on('click', '.remove-language', function() {
            var sectionId = $(this).data('section-id');
            var tranlsationQuestionId = $(this).data('translation-question-id');

            if (tranlsationQuestionId) {
                // If the row is associated with a question ID (existing question)
                var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                $.ajax({
                    url: '/questions/' + tranlsationQuestionId + '/translation-delete',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(data) {
                        $('#language-section-' + sectionId).remove();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    }
                });
            } else {
                $('#language-section-' + sectionId).remove();
            }
        });

        $('#fileInput{{ $question->id }}').on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview{{ $question->id }}').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        var quillQuestion = new Quill('.editor-question', {
            theme: 'snow'
        });

        $('form').on('submit', function() {
            // Set the hidden input's value to the content of the Quill editor (HTML)
            $('.question-input').val(quillQuestion.root.innerHTML);
        });

    });
</script>
@endsection