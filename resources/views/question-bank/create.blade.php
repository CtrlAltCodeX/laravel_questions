@extends('layouts.app')
@php
$dropdown_list = [
    //'Select Language' => $languages,
    'Select Category' => $categories,
    'Select Sub Category' => [],
    'Select Subject' => [],
    'Select Topic' => [],
]
@endphp
@section('content')
<form id="question-form" action="{{ route('question.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Question Bank</h1>
    <div class="grid gap-5 grid-cols-5">
        @foreach ($dropdown_list as $moduleName => $module)
        @php
        $id = strtolower(Str::slug($moduleName, '_'));
        $moduleKey = trim(explode('Select', $moduleName)[1]);
        @endphp
        <div class="mb-5">
            <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$moduleName}}</label>
            <select id="{{ $id }}" name="module[{{ $moduleKey }}][]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required-field">
                <option value="">{{$moduleName}}</option>
                @foreach($module as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </select>
            <div class="text-red-500 text-xs mt-1 validation-msg"></div> <!-- Validation Message -->
            @error('module.' . $moduleKey)
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>
        @endforeach
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5">
        <div id="input-rows"></div>
        <div class="flex justify-end gap-x-5 mt-5">
            <button type="button" id="add-row" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add New</button>
            <button type="submit" id="save-btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var languageId = $('#select_language').val();
        var categoryId = $('#select_category').val();
        var subCategoryId = $('#select_sub_category').val();
        var subjectId = $('#select_subject').val();
        var topicId = $('#select_topic').val();
        let rowCounter = 0;
        var languageCount = 0;

        $(document).on('click', '#addLanguage', function() {
            languageCount++;

            var qno = $('input[name="qno"]').last().val() || '';
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
                    <div>
                        <div class="border-t border-gray-300 mt-5"></div>
                        <div class="flex justify-end">
                            <button type="button" class="remove-language text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800" data-section-id="${languageCount}">
                                X
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center text-center mt-5 language-section">
                        <!-- Image Upload -->
                        <div class="col-span-2">
                            <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="qno[]" disabled
                                    placeholder="Question No." value="${qno}"/>
                        </div>

                        <!-- Question Field -->
                        <div class="col-span-4 text-left">
                            <div class='mb-2'>
                                <div class="editor-question required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
                        <div class="col-span-5 grid gap-2 w-[40%]">
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
                </div>
            `;

            $('#languages-container').append(newLanguage);

            $('#languages-container').removeClass('hidden');

            var quillQuestion = new Quill('.editor-question', {
                theme: 'snow'
            });

            $('form').on('submit', function() {
                // Set the hidden input's value to the content of the Quill editor (HTML)
                $('.question-input').val(quillQuestion.root.innerHTML);
            });
        });


        // Remove language section
        $(document).on('click', '.remove-language', function() {
            var sectionId = $(this).data('section-id');
            $('#language-section-' + sectionId).remove();
        });

        function fetchQuestions() {
            languageId = $('#select_language').val();
            categoryId = $('#select_category').val();
            subCategoryId = $('#select_sub_category').val();
            subjectId = $('#select_subject').val();
            topicId = $('#select_topic').val();


            if (categoryId && subCategoryId || subjectId || topicId) {
                $('#input-rows').empty();
                var newRow = `
                    <div class="input-row pb-5 border-b-2">
                        <div class="col-span-1 flex justify-end">
                            <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                X
                            </button>
                        </div>    
                        <div class="flex flex-wrap gap-4 items-center text-center mt-5">
                            <input type="hidden" name="id" value="" />

                            <!-- Image Upload -->
                            <div class="flex flex-col w-[15%]">
                                <div class="flex col-2 gap-x-4">
                                    <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="qno"
                                                placeholder="Question No."/>

                                    <input type="text" class="w-full mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="photo_link"
                                            placeholder="photo link"/>
                                </div>
                                <div class='mb-2'>
                                    <div class="relative col-span-2 text-left">
<<<<<<< HEAD
                                        <input type="hidden" id="photo-new" />
=======
                                        <input type="hidden" id="photo-new" name="photo" value="" />
>>>>>>> 8faa71a222ba96f2b31eeec9fd581a54b6a34131
                                        <input type="file" accept="image/*" name="photo" class="required-field file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput-new" />
                                        <button type="button" id="fileButton-new" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            Upload Photo
                                        </button>
                                    </div>
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                            </div>

                            <!-- Question Field -->
                            <div class="text-left w-[40%]">
                                <div class='mb-2'>
                                    <div id="editor-question" class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="question[]">
                                    </div>
                                    <input type="hidden" name="question[]" class="question-input" />
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                            </div>

                            <!-- Options A-D -->
                            <div class="grid grid-cols-2 gap-2 w-[40%]">
                                <div>
                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="option_a[]" 
                                            placeholder="Option A" />
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>    
                                <div>
                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="option_b[]" 
                                    placeholder="Option B" />
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                                <div>
                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="option_c[]" 
                                    placeholder="Option C" />
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                                <div>
                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="option_d[]" 
                                    placeholder="Option D" />
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                            </div>

                            <!-- Answer Field -->
                            <div class="col-span-2 w-[15%]">
                                <div>
                                    <select class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="answer">
                                        <option value="">Select Answer</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                                
                                <div class="mt-3">
                                    <select class="bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                            name="level">
                                            <option value="">Select Level</option>
                                            <option value="1">Easy</option>
                                            <option value="2">Medium</option>
                                            <option value="3">Hard</option>
                                    </select>
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                            </div>

                            <!-- Notes and Level -->
                            <div class="col-span-5 grid gap-2 w-[40%]">
                                <div>
                                    <textarea class="w-full required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="notes[]" 
                                        placeholder="Notes" 
                                        rows="3" cols="3"></textarea>
                                    <div class="text-red-500 text-xs validation-msg"></div>
                                </div>
                            </div>

                            <div class="col-span-3">
                                <select id="selectlangauge" name="language[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Language</option>
                                    @foreach($languages as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div id="languages-container" class="ms-5">
                        </div>

                        <div class="flex justify-start gap-x-5">
                            <button type="button" id="addLanguage" class="my-5 text-black border-black border bg-white hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-black font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center ">Add Language</button>
                        </div>
                    </div>
                `;

                $('#input-rows').append(newRow);

                var quillQuestion = new Quill('#editor-question', {
                    theme: 'snow'
                });

                $('form').on('submit', function() {
                    // Set the hidden input's value to the content of the Quill editor (HTML)
                    $('.question-input').val(quillQuestion.root.innerHTML);
                });

                attachFileInputHandlers();
            }
<<<<<<< HEAD
=======
            {{-- else{
                $.ajax({
                    url: '{{ route("questions") }}',
                    method: 'GET',
                    data: {
                        'language_id': languageId,
                        'category_id': categoryId,
                        'sub_category_id': subCategoryId,
                        'subject_id': subjectId,
                        'topic_id': topicId,
                    },
                    success: function(data) {
                        $('#input-rows').empty();
                        if (data.length > 0) {
                            data.forEach(function(question) {
                                var newRow = `
                                    <div class="input-row pb-5 border-b-2">
                                        <div class="col-span-1 flex justify-end">
                                            <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                                X
                                            </button>
                                        </div>    
                                        <div class="flex flex-wrap gap-4 items-center text-center mt-5">
                                            <input type="hidden" name="id[]" value="${question.id}" />

                                            <!-- Image Upload -->
                                            <div class="flex flex-col w-[15%]">
                                                <div class='mb-2'>
                                                    <input type="text" class="required-field w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                        name="photo_link[]" value="${question.photo_link}"
                                                        placeholder="Photo Link" />
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                                <div class='mb-2'>
                                                    <div class="relative col-span-2 text-left">
                                                        <input type="hidden" id="photo-${question.id}" name="photo[]" value="${question.photo}" />
                                                        <input type="file" accept="image/*" name="photo[]" class="required-field file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput-${question.id}" />
                                                        <button type="button" id="fileButton-${question.id}" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            ${question.photo ?? ' '} 
                                                        </button>
                                                    </div>
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                            </div>

                                            <!-- Question Field -->
                                            <div class="text-left w-[40%]">
                                                <div class='mb-2'>
                                                    <textarea class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                            name="question[]"
                                                            placeholder="Enter your question here" 
                                                            rows="3">${question.question}</textarea>
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                            </div>

                                            <!-- Options A-D -->
                                            <div class="grid grid-cols-2 gap-2 w-[40%]">
                                                <div>
                                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                            name="option_a[]" value="${question.option_a}" 
                                                            placeholder="Option A" />
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>    
                                                <div>
                                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="option_b[]" value="${question.option_b}" 
                                                    placeholder="Option B" />
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                                <div>
                                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="option_c[]" value="${question.option_c}" 
                                                    placeholder="Option C" />
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                                <div>
                                                    <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="option_d[]" value="${question.option_d}"
                                                    placeholder="Option D" />
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                            </div>

                                            <!-- Answer Field -->
                                            <div class="col-span-2 w-[15%]">
                                                <div>
                                                    <select class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                            name="answer[]">
                                                        <option value="">Select Answer</option>
                                                            ${['A', 'B', 'C', 'D'].map(option => `
                                                                <option value="${option}" ${option === question.answer ? 'selected' : ''}>${option}</option>
                                                            `).join('')}
                                                    </select>
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>

                                                <div>
                                                    <input type="number" class="required-field mt-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="level[]" value="${question.level}"
                                                    placeholder="Level" />
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                            </div>

                                            <!-- Notes and Level -->
                                            <div class="col-span-5 grid gap-2 w-[40%]">
                                                <div>
                                                    <textarea class="w-full required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                        name="notes[]"
                                                        placeholder="Notes" 
                                                        rows="3" cols="3">${question.notes}</textarea>
                                                    <div class="text-red-500 text-xs validation-msg"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#input-rows').append(newRow);
                            });

                            attachFileInputHandlers();

                        } else {
                            var newRow = `
                                <div class="input-row pb-5 border-b-2">
                                    <div class="col-span-1 flex justify-end">
                                        <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                            X
                                        </button>
                                    </div>    
                                    <div class="flex flex-wrap gap-4 items-center text-center mt-5">
                                        <input type="hidden" name="id[]" value="" />

                                        <!-- Image Upload -->
                                        <div class="flex flex-col w-[15%]">
                                            <div class='mb-2'>
                                                <input type="text" class="required-field w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="photo_link[]" 
                                                    placeholder="Photo Link" />
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                            <div class='mb-2'>
                                                <div class="relative col-span-2 text-left">
                                                    <input type="hidden" id="photo-new" name="photo[]" value="" />
                                                    <input type="file" accept="image/*" class="required-field file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput-new" />
                                                    <button type="button" id="fileButton-new" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        Upload Photo
                                                    </button>
                                                </div>
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                        </div>

                                        <!-- Question Field -->
                                        <div class="text-left w-[40%]">
                                            <div class='mb-2'>
                                                <textarea class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                        name="question[]" 
                                                        placeholder="Enter your question here" 
                                                        rows="3"></textarea>
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                        </div>

                                        <!-- Options A-D -->
                                        <div class="grid grid-cols-2 gap-2 w-[40%]">
                                            <div>
                                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                        name="option_a[]" 
                                                        placeholder="Option A" />
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>    
                                            <div>
                                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_b[]" 
                                                placeholder="Option B" />
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                            <div>
                                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_c[]" 
                                                placeholder="Option C" />
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                            <div>
                                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="option_d[]" 
                                                placeholder="Option D" />
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                        </div>

                                        <!-- Answer Field -->
                                        <div class="col-span-2 w-[15%]">
                                            <div>
                                                <select class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                        name="answer[]">
                                                    <option value="">Select Answer</option>
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                </select>
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>

                                            <div>
                                                <input type="number" class="required-field mt-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                name="level[]" 
                                                placeholder="Level" />
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                        </div>

                                        <!-- Notes and Level -->
                                        <div class="col-span-5 grid gap-2 w-[40%]">
                                            <div>
                                                <textarea class="w-full required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                    name="notes[]" 
                                                    placeholder="Notes" 
                                                    rows="3" cols="3"></textarea>
                                                <div class="text-red-500 text-xs validation-msg"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $('#input-rows').append(newRow);

                            attachFileInputHandlers();
                        }
                    }
                });
            } --}}

>>>>>>> 8faa71a222ba96f2b31eeec9fd581a54b6a34131
        }

        fetchQuestions();

        function attachFileInputHandlers() {
            // Attach event listener to each file input
            $('.file-input').each(function() {
                var fileInput = $(this);
                var questionId = fileInput.attr('id').replace('fileInput-', '');
                var photoInput = $('#photo-' + questionId);
                var fileButton = $('#fileButton-' + questionId);


                fileInput.on('change', function(event) {
                    console.log(fileInput, questionId, photoInput, fileButton);
                    var file = event.target.files[0];
                    if (file) {
                        // Get the file name and truncate it to 20 characters
                        var fileName = file.name.substring(0, 20) + (file.name.length > 20 ? '...' : '');
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
            var isValid = true;

            if (categoryId && subCategoryId || subjectId || topicId) {
                // Show confirmation alert
                if (confirm('Do you want to save the current question and create another one?')) {
                    // Submit the form
                    $('#question-form').submit();

                    // Reload the page after form submission
                    $('#your-form-id').on('submit', function(e) {
                        e.preventDefault(); // Prevent the default form submission
                        $.ajax({
                            type: $(this).attr('method'),
                            url: $(this).attr('action'),
                            data: $(this).serialize(),
                            success: function(response) {
                                // Reload the page after successful form submission
                                location.reload();
                            },
                            error: function(response) {
                                // Handle error
                                alert('An error occurred while saving the question.');
                            }
                        });
                    });
                } else {
                    // If the user cancels, do nothing
                    return;
                }
            } else {
                alert('Please select a category to add a question');
            }
<<<<<<< HEAD
=======
            
            
{{--             
            
            
            
            rowCounter++;
            var newRow = $(`
                <div class="input-row pb-5 border-b-2">
                    <div class="col-span-1 flex justify-end">
                        <button type="button" class="remove-question text-red-700 hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm w-8 h-8 flex justify-center items-center dark:text-red-500 dark:hover:bg-red-700 dark:focus:ring-red-800">
                            X
                        </button>
                    </div>    
                    <div class="flex flex-wrap gap-4 items-center text-center mt-5">
                        <input type="hidden" name="id[]" value="" />

                        <!-- Image Upload -->
                        <div class="flex flex-col w-[15%]">
                            <div class='mb-2'>
                                <input type="text" class="required-field w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="photo_link[]" value=""
                                    placeholder="Photo Link" />
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                            <div class='mb-2'>
                                <div class="relative col-span-2 text-left">
                                    <input type="hidden" id="photo-new${rowCounter}" name="photo[]" value="" />
                                    <input type="file" accept="image/*" name='photo[]' class="required-field file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="fileInput-new${rowCounter}" />
                                    <button type="button" id="fileButton-new${rowCounter}" class="custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        Upload Photo
                                    </button>
                                </div>
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                        </div>

                        <!-- Question Field -->
                        <div class="text-left w-[40%]">
                            <div class='mb-2'>
                                <textarea class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="question[]" 
                                        placeholder="Enter your question here" 
                                        rows="3"></textarea>
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                        </div>

                        <!-- Options A-D -->
                        <div class="grid grid-cols-2 gap-2 w-[40%]">
                            <div>
                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        name="option_a[]" 
                                        placeholder="Option A" />
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>    
                            <div>
                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                name="option_b[]" 
                                placeholder="Option B" />
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                            <div>
                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                name="option_c[]" 
                                placeholder="Option C" />
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                            <div>
                                <input type="text" class="required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                name="option_d[]" 
                                placeholder="Option D" />
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                        </div>

                        <!-- Answer Field -->
                        <div class="col-span-2 w-[15%]">
                            <select class="required-field bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="answer[]">
                                <option value="">Select Answer</option>
                                ${['A', 'B', 'C', 'D'].map(option => `
                                    <option value="${option}">${option}</option>
                                `).join('')}
                            </select>

                            <div>
                                <input type="number" class="required-field mt-2 w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                name="level[]" 
                                placeholder="Level" />
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                            <div class="text-red-500 text-xs validation-msg"></div>
                        </div>

                        <!-- Notes and Level -->
                        <div class="col-span-5 grid gap-2 w-[40%]">
                            <div>
                                <textarea class="w-full required-field bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    name="notes[]" 
                                    placeholder="Notes" 
                                    rows="3" cols="3"></textarea>
                                <div class="text-red-500 text-xs validation-msg"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Append the new row to the container
            $('#input-rows').append(newRow);

            // Attach file input handlers to the new row
            attachFileInputHandlers(); --}}
>>>>>>> 8faa71a222ba96f2b31eeec9fd581a54b6a34131
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
        });

        $('#save-btn').click(function(e) {
            e.preventDefault(); // Prevent the form from submitting immediately
            var isValid = true;

            if (categoryId && subCategoryId || subjectId || topicId) {
                $('#question-form').submit();
            } else {
                alert('Please select a category to add a question');
            }
        });


    });
</script>
@endsection