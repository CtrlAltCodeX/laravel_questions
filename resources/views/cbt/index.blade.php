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

        <div id="sets-wrapper">
            <div class="flex gap-x-5 items-center">
                <input type="text" id="apiLink" class="w-1/2 px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="search" id="search" placeholder="Link">
                <button id="copy-button" type="button" class="copy-button text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
            </div>
        </div>

        <div class="flex gap-x-10 items-center mt-5">
                @csrf
                <input type="hidden" name="access_token" id="access_token" value={{session('access_token')}}>
                <input type="hidden" name="api_link" id="api_link">
        </div>

    </form>

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
        const baseUrl = "{{ url('api/quiz') }}";

        const dropdowns = document.querySelectorAll('select');
        const apiLink = document.getElementById('apiLink');

        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('change', (e) => {
                const params = new URLSearchParams();
                dropdowns.forEach(d => {
                    if (d.value) {
                        params.append(d.name, d.value);
                    }
                });
                apiLink.value = `${baseUrl}?${params.toString()}`;
                document.getElementById('api_link').value = apiLink.value;
            });
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
                            `
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5 my-5 w-1/3">
                                    
                            `;

                        subjects.forEach((subject, index) => {
                            questionTable += 
                            ` 
                                <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <tbody>
                                        <tr>
                                            <td class="p-2" data-column="total-questions">${subject.questions}</td>
                                            <td class="p-2" data-column="subject">${subject.name}</td>
                                            <td class="p-2 w-28" data-column="questions">
                                                <input id="questions_${index}" name="subjects[${subject.id}]" type="text" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" id="questions" placeholder="questions"> 
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                            <td colspan="3" class="h-[1px] text-center"></td>
                                        </tr>
                            `;
                        });

                        questionTable += `
                                        <tr>
                                            <td class="p-2" data-column="question">Set</td>
                                            <td class="p-2" data-column=""></td>
                                            <td class="p-2" data-column="sets">
                                                <input type="number" name="sets" id="sets" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="sets" value="${length}" id="sets" placeholder="sets"> 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            `;

                        questionTable += '</div>';

                        $('#question-data').html(questionTable);

                        for(var i = 0; i < length; i++){
                            var set = i + 1;
                            var setDiv = document.createElement('div');

                            setDiv.classList.add('flex', 'gap-x-5', 'items-center');

                            var sets_wrapper_div = document.getElementById('sets-wrapper');

                            sets_wrapper_div.innerHTML = '';
                            
                            setDiv.innerHTML = '';
                            
                            var params = new URLSearchParams();
                            {{-- params.append('subject', subject.id); --}}
                            params.append('Set', set);
                            
                            var apiLinkValue = apiLink.value + '&';
                            apiLinkValue += `${params.toString()}`;

                            setDiv.innerHTML = `
                                <input type="text" class="w-1/2 px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="apiLink-set-${set}" id="apiLink-set-${set}" value="${apiLinkValue}" placeholder="Link">
                                <button id="copy-button" data-index="${i}" class="copy-button text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
                            `;

                            sets_wrapper_div.appendChild(setDiv);
                        }

                        $(document).on('click', '.copy-button', function() {
                            var input = $(this).prev('input');
                            var index = $(this).data('index');

                            input.select();
                            document.execCommand('copy');

                            $('#selected_subject').val(subjects[index].id);
                        });

                        // Update set URLs when questions input changes
                        subjects.forEach((subject, index) => {
                            $(`#questions_${index}`).on('input', function() {
                                var set = index + 1;
                                var params = new URLSearchParams();
                                params.append('Subject', subject.id);
                                params.append('Set', set);
                                params.append('Limit', $(this).val());

                                var apiLinkValue = apiLink.value + '&' + params.toString();
                                $(`#apiLink-set-${set}`).val(apiLinkValue);
                            });
                        });
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

                        var length = subjects1Details.length;

                        var questionTable = 
                            `
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5 my-5 w-1/2">
                                    
                            `;

                        subjects1Details.forEach((subject1, index1) => {
                            subjects2Details.forEach((subject2, index2) => {
                                questionTable += 
                                `
                                    <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <tbody>
                                            <tr>
                                                <td class="p-2" data-column="total-questions">${subject1.questions}</td>
                                                <td class="p-2" data-column="subject">${subject1.name} | ${subject2.name}</td>
                                                <td class="p-2 w-28" data-column="questions">
                                                    <input id="questions_${index1}" name="subjects[${subject2.id}]" type="text" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" placeholder="questions"> 
                                                </td>
                                            </tr>
                                            <tr class="bg-gray-200 dark:bg-gray-700">
                                                <td colspan="3" class="h-[1px] text-center"></td>
                                            </tr>
                                `;
                            });
                        });

                        questionTable += `
                                        <tr>
                                            <td class="p-2" data-column="question">Set</td>
                                            <td class="p-2" data-column=""></td>
                                            <td class="p-2" data-column="sets">
                                                <input type="number" name="sets" id="sets" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="sets" id="sets" value="${length}" placeholder="sets"> 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            `;

                        questionTable += '</div>';

                        $('#question-data').html(questionTable);


                        for(var i = 0; i < length; i++){
                            var set = i + 1;
                            var setDiv = document.createElement('div');

                            setDiv.classList.add('flex', 'gap-x-5', 'items-center');

                            var sets_wrapper_div = document.getElementById('sets-wrapper');

                            sets_wrapper_div.innerHTML = '';
                            
                            setDiv.innerHTML = '';
                            
                            var params = new URLSearchParams();
                            {{-- params.append('subject', subject.id); --}}
                            params.append('Set', set);
                            
                            var apiLinkValue = apiLink.value + '&';
                            apiLinkValue += `${params.toString()}`;

                            setDiv.innerHTML = `
                                <input type="text" class="w-1/2 px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="apiLink-set-${set}" id="apiLink-set-${set}" value="${apiLinkValue}" placeholder="Link">
                                <button id="copy-button" data-index="${i}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
                            `;

                            sets_wrapper_div.appendChild(setDiv);
                        }

                        // Update set URLs when questions input changes
                        subjects2Details.forEach((subject, index) => {
                            $(`#questions_${index}`).on('input', function() {
                                var set = index + 1;
                                console.log('Limit:', $(`#apiLink-set-${set}`).val(), set);
                                var params = new URLSearchParams();
                                params.append('Subject_2', subject.id);
                                params.append('Set', set);
                                params.append('Limit', $(this).val());

                                var apiLinkValue = apiLink.value + '&' + params.toString();
                                $(`#apiLink-set-${set}`).val(apiLinkValue);
                            });
                        });

                        $(document).on('click', '.copy-button', function() {
                            var input = $(this).prev('input');
                            var index = $(this).data('index');

                            var apiLinkValue = $('#apiLink').val() + '&' + params.toString();
                            input.val(apiLinkValue);

                            input.select();
                            document.execCommand('copy');

                            $('#selected_subject').val(subjects1Details[index].id);
                            $('#selected_subject2').val(subjects2Details[index].id);
                        });

                        {{-- $('#sets').change(function(){
                            var sets = $(this).val();
                            var apiLink = $('#apiLink').val();
                            var sets_wrapper_div = document.getElementById('sets-wrapper');

                            sets_wrapper_div.innerHTML = '';

                            for(var i = 0; i < sets; i++){
                                var set = i + 1;
                                var setDiv = document.createElement('div');
                                setDiv.classList.add('flex', 'gap-x-5', 'items-center');
                                setDiv.innerHTML = `
                                    <input type="text" id="apiLink" class="w-1/2 px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="apiLink-set-${set}" value="${apiLink}" placeholder="Link">
                                    <button id="copy-button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
                                `;
                                sets_wrapper_div.appendChild(setDiv);
                            }
                        }); --}}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching subjects:', error); // Debugging: Log any errors
                    }
                });
            }
        });       
    });
</script>

@endsection