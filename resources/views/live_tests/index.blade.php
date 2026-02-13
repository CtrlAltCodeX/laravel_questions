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
                <th scope="col" class="px-6 py-3">Schedule</th>
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
                <td class="px-6 py-4 text-xs">{{ $test->schedule ? $test->schedule->format('d M Y, h:i A') : 'N/A' }}</td>
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
                    <select id="select_sub_category" name="sub_category_ids[]" multiple="multiple" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
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

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Title</label>
                    <input type="text" name="title" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter test title">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Schedule Date & Time</label>
                    <input type="datetime-local" name="schedule" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
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
                    <span>Fetched Total Questions: <span id="question_count">0</span></span>
                    <!-- <span class="text-blue-600">Question Limit: <span id="question_limit_val">0</span></span> -->
                </div>
            </div>

            <!-- Questions Container (Accordions) -->
            <div id="questions_container" class="mt-4 space-y-2 hidden" style="max-height: 450px; overflow-y: auto;">
                <!-- Subject accordions will be injected here -->
            </div>

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
        placeholder: 'Select Sub Categories'
    });

    $('#createButton').click(function() {
        $('#liveTestForm')[0].reset();
        $('#test_id').val('');
        $('#modalTitle').text('Create Live Test');
        $('#saveButton').text('Save Live Test');
        $('#select_language').trigger('change');
        $('#select_sub_category').val(null).trigger('change');
        $('#questions_container').addClass('hidden').empty();
        $('#stats_area').addClass('hidden');
        $('#modal').css('display', 'flex').hide().fadeIn();
    });

    var isAutoPopulating = false;

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

    function loadSubCategories(categoryId, selectedIds = null, callback = null) {
        if (!categoryId) return;
        $.ajax({
            url: '/get-subcategories/' + categoryId,
            method: 'GET',
            success: function(data) {
                $('#select_sub_category').empty();
                $.each(data, function(key, value) {
                    $('#select_sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                if (selectedIds) {
                    var ids = Array.isArray(selectedIds) ? selectedIds.map(String) : [String(selectedIds)];
                    $('#select_sub_category').val(ids).trigger('change');
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
                if(data.schedule) {
                    var scheduleDate = new Date(data.schedule);
                    scheduleDate.setMinutes(scheduleDate.getMinutes() - scheduleDate.getTimezoneOffset());
                    $('input[name="schedule"]').val(scheduleDate.toISOString().slice(0, 16));
                }
                
                $('input[name="toppers_star"]').val(data.toppers_star);
                $('input[name="toppers"]').val(data.toppers);
                $('input[name="participant_star"]').val(data.participant_star);
                $('input[name="mode"][value="' + data.mode + '"]').prop('checked', true);

                // Re-populating Cascading selects step-by-step
                $('#select_language').val(data.language_id);
                loadCategories(data.language_id, data.category_id, function() {
                    loadSubCategories(data.category_id, data.sub_category_id, function() {
                        // Finally fetch questions and auto-check
                        fetchQuestions(function() {
                            if (data.question_ids) {
                                var qIds = Array.isArray(data.question_ids) ? data.question_ids.map(String) : [];
                                $.each(qIds, function(i, qid) {
                                    $('.question-checkbox[value="' + qid + '"]').prop('checked', true);
                                });
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

    $('input[name="mode"]').change(function() {
        if ($(this).val() == 'manual') {
            alert('Manual mode coming soon');
            $('input[name="mode"][value="auto"]').prop('checked', true);
        }
    });

    function fetchQuestions(callback = null) {
        var languageId = $('#select_language').val();
        var categoryId = $('#select_category').val();
        var subCategoryIds = $('#select_sub_category').val();

        if (languageId && categoryId && subCategoryIds && subCategoryIds.length > 0) {
            $.ajax({
                url: "{{ route('live-tests.get-questions') }}",
                method: 'GET',
                data: {
                    language_id: languageId,
                    category_id: categoryId,
                    sub_category_ids: subCategoryIds
                },
                success: function(data) {
                    $('#stats_area').removeClass('hidden');
                    $('#subject_count').text(data.subject_count);
                    $('#question_count').text(data.question_count);
                    $('#question_limit_val').text(data.limit);

                    $('#questions_container').removeClass('hidden').empty();

                    if (data.subjects.length > 0) {
                        $.each(data.subjects, function(key, subject) {
                            var accordionHtml = `
                                <div class="subject-item border rounded-lg overflow-hidden mb-2">
                                    <button type="button" class="subject-header w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 transition-colors font-semibold text-sm">
                                        <span>${subject.name} (<span class="count">${subject.questions.length}</span> questions)</span>
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

    // Select All for Subject
    $(document).on('change', '.select-all-subject', function() {
        var subjectId = $(this).data('subject-id');
        $('.subject-questions-' + subjectId).prop('checked', $(this).is(':checked'));
    });

    // Form Submission
    $('#liveTestForm').submit(function(e) {
        e.preventDefault();

        var totalFetched = parseInt($('#question_count').text());
        var totalSelected = $('.question-checkbox:checked').length;

        if (totalFetched > 0 && totalSelected < totalFetched) {
            alert("Please select all " + totalFetched + " questions to continue.");
            return;
        }

        if (totalFetched === 0) {
            alert("Please select categories and sub categories to fetch questions first.");
            return;
        }

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
