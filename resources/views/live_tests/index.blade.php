@extends('layouts.app')

@section('title', 'Live Test')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Live Test</h1>

    <div class="flex justify-end items-center gap-2">
        <button id="createButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Create
        </button>
        <input type="text" id="searchFilter" placeholder="Search..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
    </div>
</div>

<div class="mt-6 overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">Language</th>
                <th scope="col" class="px-6 py-3">Category</th>
                <th scope="col" class="px-6 py-3">Questions</th>
                <th scope="col" class="px-6 py-3">Subjects</th>
                <th scope="col" class="px-6 py-3">Start Date</th>
                <th scope="col" class="px-6 py-3">End Date</th>
                <th scope="col" class="px-6 py-3">Mode</th>
                <th scope="col" class="px-6 py-3 text-center">Status</th>
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($liveTests as $test)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                <td class="px-6 py-4 font-bold">{{ $test->title ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $test->language->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $test->category->name ?? '-' }}</td>
                <td class="px-6 py-4 text-center">{{ count($test->question_ids ?? []) }}</td>
                <td class="px-6 py-4 text-center font-bold text-blue-600">{{ $test->subject_count }}</td>
                <td class="px-6 py-4 text-xs">{{ $test->start_date ? $test->start_date->format('d M Y, h:i A') : 'N/A' }}</td>
                <td class="px-6 py-4 text-xs">{{ $test->end_date ? $test->end_date->format('d M Y, h:i A') : 'N/A' }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs {{ $test->mode == 'auto' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($test->mode) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded text-xs {{ $test->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $test->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <button type="button" class="text-blue-600 hover:underline edit-test" data-id="{{ $test->id }}">Edit</button>
                        <form action="{{ route('live-tests.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $liveTests->links() }}
    </div>
</div>

<!-- Create Modal -->
<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 80%; max-height: 90vh; margin: auto; padding: 24px; position: relative; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Create Live Test</h2>
            <button id="closeModal" style="background: none; border: 1px solid black; cursor: pointer; color: #6B7280; border-radius: 100%; width: 25px;">X</button>
        </div>

        <form id="liveTestForm">
            @csrf
            <input type="hidden" name="test_id" id="test_id">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Language</label>
                    <select id="select_language" name="language_id" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Language</option>
                        @foreach($languages as $lang)
                        <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Main Category</label>
                    <select id="select_category" name="category_id" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                    </select>
                </div>

                <div class="col-span-2 space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Sub Category</label>
                    <select id="select_sub_category" name="sub_category_id" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </select>
                </div>

                <div class="col-span-2 mb-2">
                    <label class="text-sm font-semibold text-gray-700">Mode</label>
                    <div class="flex gap-4 mt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="mode" value="auto" checked class="w-4 h-4 text-blue-600"> Auto
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="mode" value="manual" class="w-4 h-4 text-blue-600"> Manual
                        </label>
                    </div>
                </div>

                <!-- Manual Mode Buttons -->
                <div id="manual_mode_actions" class="col-span-2 hidden bg-gray-50 p-4 rounded-xl border border-gray-200 flex gap-4">
                    <button type="button" id="btn_download_template" class="flex-1 bg-white border-2 border-primary text-primary px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa fa-download"></i> Download Template
                    </button>
                    <button type="button" id="btn_upload_excel" class="flex-1 bg-white border-2 border-primary text-primary px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-50 transition-all flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa fa-file-excel"></i> Upload Excel File
                    </button>
                    <input type="file" id="excel_file_input" class="hidden" accept=".xlsx, .xls">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Title</label>
                    <input type="text" name="title" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter test title">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Start Date & Time</label>
                    <input type="datetime-local" name="start_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">End Date & Time</label>
                    <input type="datetime-local" name="end_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Toppers Star</label>
                    <input type="number" name="toppers_star" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Toppers Count</label>
                    <input type="number" name="toppers" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                </div>

                <div class="col-span-2 space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Participant Star</label>
                    <input type="number" name="participant_star" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                </div>
            </div>

            <!-- Summary Area -->
            <div id="stats_area" class="mt-4 mb-4 p-4 bg-gray-50 rounded-lg hidden">
                <div class="flex justify-between items-center text-sm font-medium">
                    <span>Subjects: <span id="subject_count">0</span></span>
                    <span>Fetched Questions: <span id="question_count">0</span></span>
                    <span class="text-green-600">Total Selected: <span id="selected_count_val">0</span></span>
                </div>
            </div>

            <!-- Questions Container (Accordions) -->
            <div id="questions_container" class="mt-4 space-y-2 hidden" style="max-height: 450px; overflow-y: auto;">
                <!-- Subject accordions will be injected here -->
            </div>

            <!-- Manual Preview Table -->
            <div id="manual_preview_container" class="mt-4 hidden">
                <h3 class="text-sm font-bold text-gray-800 mb-2 flex justify-between items-center">
                    Excel Preview
                    <span class="text-xs font-normal text-gray-500" id="manual_question_count_label">0 questions found</span>
                </h3>
                <div class="border rounded-lg overflow-x-auto" style="max-height: 400px;">
                    <table class="w-full text-[11px] text-left text-gray-500 min-w-[800px]">
                        <thead class="bg-gray-100 uppercase text-gray-700 font-bold sticky top-0">
                            <tr>
                                <th class="px-2 py-2">Lang ID</th>
                                <th class="px-2 py-2">Cat ID</th>
                                <th class="px-2 py-2">SubCat ID</th>
                                <th class="px-2 py-2">Sub ID</th>
                                <th class="px-2 py-2">Question</th>
                                <th class="px-2 py-2">Option A</th>
                                <th class="px-2 py-2">Option B</th>
                                <th class="px-2 py-2">Option C</th>
                                <th class="px-2 py-2">Option D</th>
                                <th class="px-2 py-2">Answer</th>
                                <th class="px-2 py-2">Photo</th>
                            </tr>
                        </thead>
                        <tbody id="manual_preview_body">
                            <!-- JS content here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <input type="hidden" name="manual_questions" id="manual_questions_data">

            <div class="mt-6 pt-4 border-t flex justify-end gap-3 bg-white">
                <button type="button" id="cancelModal" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancel</button>
                <button type="submit" id="saveButton" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 disabled:opacity-50">Save Live Test</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#select_sub_category').select2({
        width: '100%',
        placeholder: 'Select Sub Category'
    });

    $('#createButton').click(function() {
        $('#liveTestForm')[0].reset();
        $('#test_id').val('');
        $('#modalTitle').text('Create Live Test');
        $('#saveButton').text('Save Live Test');
        $('#select_language').trigger('change');
        $('#select_sub_category').val('').trigger('change');
        $('#questions_container').addClass('hidden').empty();
        $('#stats_area').addClass('hidden');
        $('#manual_preview_container').addClass('hidden');
        $('#manual_mode_actions').addClass('hidden');
        $('#manual_preview_body').empty();
        manualQuestions = [];
        $('#manual_questions_data').val('');
        $('#modal').css('display', 'flex').hide().fadeIn();
    });

    var isAutoPopulating = false;
    var manualQuestions = [];

    // --- Mode Toggle Logic ---
    $('input[name="mode"]').change(function() {
        var mode = $(this).val();
        if (mode === 'manual') {
            $('#manual_mode_actions').removeClass('hidden');
            $('#questions_container').addClass('hidden');
            $('#stats_area').addClass('hidden');
            // If we have manual questions already, show preview
            if (manualQuestions.length > 0) {
                $('#manual_preview_container').removeClass('hidden');
            }
        } else {
            $('#manual_mode_actions').addClass('hidden');
            $('#manual_preview_container').addClass('hidden');
            // If we have auto questions container content, show it
            if ($('#questions_container').children().length > 0) {
                $('#questions_container').removeClass('hidden');
                $('#stats_area').removeClass('hidden');
            }
        }
    });

    // --- Manual Mode: Download Template ---
    $('#btn_download_template').click(function() {
        var lang = $('#select_language').val();
        var cat = $('#select_category').val();
        var subCat = $('#select_sub_category').val();

        if (!lang || !cat || !subCat) {
            alert("Please select Language, Category and Sub Category first.");
            return;
        }

        var url = "{{ route('live-tests.download-manual-template') }}?language_id=" + lang + "&category_id=" + cat + "&sub_category_id=" + subCat;
        window.location.href = url;
    });

    // --- Manual Mode: Upload & Preview ---
    $('#btn_upload_excel').click(function() {
        $('#excel_file_input').click();
    });

    $('#excel_file_input').change(function() {
        var file = this.files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('live-tests.preview-manual-data') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    renderManualPreview(response.data);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert("Error uploading file.");
            }
        });
    });

    function renderManualPreview(data) {
        manualQuestions = data;
        $('#manual_questions_data').val(JSON.stringify(data));
        
        var body = $('#manual_preview_body').empty();
        $('#manual_preview_container').removeClass('hidden');
        $('#manual_question_count_label').text(data.length + " questions found");

        $.each(data, function(i, q) {
            var row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-2 py-2">${q.language_id || '-'}</td>
                    <td class="px-2 py-2">${q.category || q.category_id || '-'}</td>
                    <td class="px-2 py-2">${q.subcategory || q.sub_category_id || '-'}</td>
                    <td class="px-2 py-2 font-medium text-gray-900">${q.subject || q.subject_id || '-'}</td>
                    <td class="px-2 py-2">${q.question || '-'}</td>
                    <td class="px-2 py-2">${q.option_a || '-'}</td>
                    <td class="px-2 py-2">${q.option_b || '-'}</td>
                    <td class="px-2 py-2">${q.option_c || '-'}</td>
                    <td class="px-2 py-2">${q.option_d || '-'}</td>
                    <td class="px-2 py-2 text-center font-bold text-green-700">${q.answer || '-'}</td>
                    <td class="px-2 py-2">${q.photo || '-'}</td>
                </tr>
            `;
            body.append(row);
        });
    }

    // --- UI Logic Functions ---
    
    function loadCategories(languageId, selectedId = null, callback = null) {
        if (!languageId) return;
        $.ajax({
            url: '/get-categories/' + languageId,
            method: 'GET',
            success: function(data) {
                $('#select_category').empty().append('<option value="">Select Category</option>');
                $.each(data, function(key, value) {
                    $('#select_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                if (selectedId) $('#select_category').val(selectedId);
                if (callback) callback();
            }
        });
    }

    function loadSubCategories(categoryId, selectedId = null, callback = null) {
        if (!categoryId) return;
        $.ajax({
            url: '/get-subcategories/' + categoryId,
            method: 'GET',
            success: function(data) {
                $('#select_sub_category').empty().append('<option value="">Select Sub Category</option>');
                $.each(data, function(key, value) {
                    $('#select_sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                if (selectedId) {
                    $('#select_sub_category').val(selectedId).trigger('change');
                }
                if (callback) callback();
            }
        });
    }

    $(document).on('click', '.edit-test', function() {
        var id = $(this).data('id');
        isAutoPopulating = true;
        $.ajax({
            url: "/live-tests/" + id + "/edit",
            method: 'GET',
            success: function(data) {
                $('#modalTitle').text('Edit Live Test');
                $('#saveButton').text('Update Live Test');
                $('#test_id').val(data.id);
                
                $('input[name="title"]').val(data.title);
                
                if(data.start_date) {
                    var startDate = new Date(data.start_date);
                    startDate.setMinutes(startDate.getMinutes() - startDate.getTimezoneOffset());
                    $('input[name="start_date"]').val(startDate.toISOString().slice(0, 16));
                }
                if(data.end_date) {
                    var endDate = new Date(data.end_date);
                    endDate.setMinutes(endDate.getMinutes() - endDate.getTimezoneOffset());
                    $('input[name="end_date"]').val(endDate.toISOString().slice(0, 16));
                }
                
                $('input[name="toppers_star"]').val(data.toppers_star);
                $('input[name="toppers"]').val(data.toppers);
                $('input[name="participant_star"]').val(data.participant_star);
                $('input[name="mode"][value="' + data.mode + '"]').prop('checked', true);

                // Re-populating Cascading selects step-by-step
                $('#select_language').val(data.language_id);
                loadCategories(data.language_id, data.category_id, function() {
                    // sub_category_id is stored as json/array in DB but we use single select now
                    var subCatId = Array.isArray(data.sub_category_id) ? data.sub_category_id[0] : data.sub_category_id;
                    loadSubCategories(data.category_id, subCatId, function() {
                        // Finally fetch questions and auto-check
                        fetchQuestions(function() {
                            if (data.mode === 'manual') {
                                $('#manual_mode_actions').removeClass('hidden');
                                $('#questions_container').addClass('hidden');
                                $('#stats_area').addClass('hidden');
                                if (data.manual_questions) {
                                    renderManualPreview(data.manual_questions);
                                }
                            } else {
                                $('#manual_mode_actions').addClass('hidden');
                                $('#manual_preview_container').addClass('hidden');
                                $('#questions_container').removeClass('hidden');
                                $('#stats_area').removeClass('hidden');
                                if (data.question_ids) {
                                    var qIds = Array.isArray(data.question_ids) ? data.question_ids.map(String) : [];
                                    $.each(qIds, function(i, qid) {
                                        $('.question-checkbox[value="' + qid + '"]').prop('checked', true);
                                    });
                                    updateSelectionCount();
                                }
                            }
                            isAutoPopulating = false; // Reset flag after all population is done
                        });
                    });
                });

                $('#modal').css('display', 'flex').hide().fadeIn();
            }
        });
    });

    $('#closeModal, #cancelModal').click(function() {
        $('#modal').fadeOut();
    });

    // Language change -> Category
    $('#select_language').change(function() {
        if (!isAutoPopulating) loadCategories($(this).val());
    });

    // Category change -> Sub Category
    $('#select_category').change(function() {
        if (!isAutoPopulating) loadSubCategories($(this).val());
    });

    // Sub Category change -> Fetch Questions (Auto Mode)
    $('#select_sub_category').on('change', function() {
        if (!isAutoPopulating) fetchQuestions();
    });


    function fetchQuestions(callback = null) {
        var languageId = $('#select_language').val();
        var categoryId = $('#select_category').val();
        var subCategoryId = $('#select_sub_category').val();

        if (languageId && categoryId && subCategoryId) {
            $.ajax({
                url: "{{ route('live-tests.get-questions') }}",
                method: 'GET',
                data: {
                    language_id: languageId,
                    category_id: categoryId,
                    sub_category_id: subCategoryId
                },
                success: function(data) {
                    var currentMode = $('input[name="mode"]:checked').val();
                    if (currentMode === 'auto') {
                        $('#stats_area').removeClass('hidden');
                        $('#questions_container').removeClass('hidden');
                    }
                    
                    $('#subject_count').text(data.subject_count);
                    $('#question_count').text(data.question_count);
                    $('#question_limit_val').text(data.limit);
                    $('#selected_count_val').text(0);

                    $('#questions_container').empty();

                    if (data.subjects.length > 0) {
                        $.each(data.subjects, function(key, subject) {
                            var accordionHtml = `
                                <div class="subject-item border rounded-lg overflow-hidden mb-2">
                                    <button type="button" class="subject-header w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 transition-colors font-semibold text-sm" data-subject-limit="${subject.limit}" data-subject-id="${subject.id}">
                                        <div class="flex items-center gap-4">
                                            <span>${subject.name} (<span class="count">${subject.questions.length}</span> questions)</span>
                                            <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs">Selection Limit: ${subject.limit}</span>
                                            <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded text-xs">Selected: <span id="selected_subject_count_${subject.id}">0</span></span>
                                        </div>
                                        <i class="fa fa-chevron-down transform transition-transform"></i>
                                    </button>
                                    <div class="subject-content hidden p-3 bg-white border-t overflow-x-auto">
                                        <div class="mb-3 flex items-center gap-2">
                                            <input type="checkbox" class="select-all-subject" data-subject-id="${subject.id}">
                                            <label class="text-xs font-bold cursor-pointer text-blue-700">Select All ${subject.name}</label>
                                        </div>
                                        <table class="w-full text-[11px] text-left text-gray-500 min-w-[800px]">
                                            <thead class="bg-gray-100 uppercase text-gray-700 font-bold">
                                                <tr>
                                                    <th class="px-2 py-2 w-10">QNo.</th>
                                                    <th class="px-2 py-2 w-1/4">Question</th>
                                                    <th class="px-2 py-2">Option A</th>
                                                    <th class="px-2 py-2">Option B</th>
                                                    <th class="px-2 py-2">Option C</th>
                                                    <th class="px-2 py-2">Option D</th>
                                                    <th class="px-2 py-2 text-center">Answer</th>
                                                    <th class="px-2 py-2 text-center">Level</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;
                            
                            $.each(subject.questions, function(qKey, q) {
                                accordionHtml += `
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-2 py-2 flex items-center gap-1 font-medium text-gray-900">
                                            <input type="checkbox" name="question_ids[]" value="${q.id}" class="question-checkbox subject-questions-${subject.id}">
                                            <span>${qKey + 1}</span>
                                        </td>
                                        <td class="px-2 py-2 font-medium text-gray-900">${q.question}</td>
                                        <td class="px-2 py-2">${q.option_a || '-'}</td>
                                        <td class="px-2 py-2">${q.option_b || '-'}</td>
                                        <td class="px-2 py-2">${q.option_c || '-'}</td>
                                        <td class="px-2 py-2">${q.option_d || '-'}</td>
                                        <td class="px-2 py-2 text-center font-bold text-green-700">${q.answer || '-'}</td>
                                        <td class="px-2 py-2 text-center"><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[10px]">${q.level || 'Easy'}</span></td>
                                    </tr>`;
                            });

                            accordionHtml += `</tbody></table></div></div>`;
                            $('#questions_container').append(accordionHtml);
                        });
                        
                        updateSelectionCount();
                        if (callback) callback();
                    } else {
                        $('#questions_container').append('<div class="text-center py-6 bg-gray-50 rounded-lg text-gray-500 font-medium">No subjects or questions found matching your criteria.</div>');
                    }
                }
            });
        }
    }

    // Toggle Accordion
    $(document).on('click', '.subject-header', function() {
        var content = $(this).siblings('.subject-content');
        var icon = $(this).find('i');
        
        $('.subject-content').not(content).addClass('hidden');
        $('.subject-header i').not(icon).removeClass('rotate-180');
        
        content.toggleClass('hidden');
        icon.toggleClass('rotate-180');
    });

    // Update Selection Count Logic
    function updateSelectionCount(subjectId = null) {
        if (subjectId) {
            var subjectCount = $('.subject-questions-' + subjectId + ':checked').length;
            $('#selected_subject_count_' + subjectId).text(subjectCount);
        } else {
            $('.subject-header').each(function() {
                var sid = $(this).data('subject-id');
                var scount = $('.subject-questions-' + sid + ':checked').length;
                $('#selected_subject_count_' + sid).text(scount);
            });
        }

        var totalCount = $('.question-checkbox:checked').length;
        $('#selected_count_val').text(totalCount);
    }

    $(document).on('click', '.question-checkbox', function(e) {
        var checkbox = $(this);
        var subjectItem = checkbox.closest('.subject-item');
        var subjectHeader = subjectItem.find('.subject-header');
        var subjectId = subjectHeader.data('subject-id');
        var subjectLimit = parseInt(subjectHeader.data('subject-limit'));
        
        var currentSubjectSelected = $('.subject-questions-' + subjectId + ':checked').length;

        if (checkbox.is(':checked') && currentSubjectSelected > subjectLimit) {
            e.preventDefault();
            checkbox.prop('checked', false);
            alert("Limit reached! You can only select up to " + subjectLimit + " questions in this subject.");
            return;
        }
        
        updateSelectionCount(subjectId);
    });

    // Select All for Subject (Modified for limit and alert)
    $(document).on('change', '.select-all-subject', function() {
        var subjectId = $(this).data('subject-id');
        var subjectItem = $(this).closest('.subject-item');
        var subjectHeader = subjectItem.find('.subject-header');
        var limit = parseInt(subjectHeader.data('subject-limit'));
        
        if ($(this).is(':checked')) {
            var subjectCheckboxes = $('.subject-questions-' + subjectId + ':not(:checked)');
            var currentSelected = $('.subject-questions-' + subjectId + ':checked').length;
            var availableSlots = limit - currentSelected;
            
            if (availableSlots <= 0) {
                $(this).prop('checked', false);
                alert("Limit reached! You can only select up to " + limit + " questions in this subject.");
                return;
            }

            var selectionMade = 0;
            subjectCheckboxes.each(function(index) {
                if (index < availableSlots) {
                    $(this).prop('checked', true);
                    selectionMade++;
                }
            });
            
            if (subjectCheckboxes.length > availableSlots) {
                alert("Only " + availableSlots + " additional questions selected to match the subject limit of " + limit);
            }
        } else {
            $('.subject-questions-' + subjectId).prop('checked', false);
        }
        updateSelectionCount(subjectId);
    });

    // Form Submission
    $('#liveTestForm').submit(function(e) {
        e.preventDefault();

        var mode = $('input[name="mode"]:checked').val();
        var hasError = false;

        if (mode === 'auto') {
            $('.subject-header').each(function() {
                var subjectHeader = $(this);
                var subjectId = subjectHeader.data('subject-id');
                var limit = parseInt(subjectHeader.data('subject-limit'));
                var selected = $('.subject-questions-' + subjectId + ':checked').length;
                var subjectName = subjectHeader.find('span:first').text();

                if (selected < limit) {
                    alert("Please select exactly " + limit + " questions for subject: " + subjectName + ". Currently selected: " + selected);
                    hasError = true;
                    return false; // Break loop
                }
            });
        } else {
            if (manualQuestions.length === 0) {
                alert("Please upload an Excel file with questions for Manual Mode.");
                hasError = true;
            }
        }

        if (hasError) return;

        var formData = $(this).serialize();
        var testId = $('#test_id').val();
        var url = testId ? "/live-tests/" + testId : "{{ route('live-tests.store') }}";
        var method = testId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: 'POST',
            data: formData + (testId ? "&_method=PUT" : ""),
            success: function(data) {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMsg = '';
                if (errors) {
                    $.each(errors, function(key, value) {
                        errorMsg += value + '\n';
                    });
                } else {
                    errorMsg = xhr.responseJSON.message || 'Error occurred while saving';
                }
                alert(errorMsg);
            }
        });
    });
});
</script>
<style>
    .rotate-180 { transform: rotate(180deg); }
    #modal { backdrop-filter: blur(2px); }
</style>
@endpush
