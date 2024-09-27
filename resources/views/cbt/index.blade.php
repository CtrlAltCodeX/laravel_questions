@extends('layouts.app')
@php 
    $dropdown_list = [
    'Select Language' => $languages,
    'Select Language-2' => $languages,
    'Select Category' => [],
    'Select SubCategory' => [],
    ];
@endphp
@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">CBT Api</h1>

    {{-- <a href="{{ route('quiz.create') }}" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</a> --}}

</div>

<div>
    <form action="{{ route('cbt.deploy') }}" method="POST" enctype="multipart/form-data">
        <div class="flex justify-between gap-x-5">
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

        <div id="question-data"></div>

        

        <div class="flex gap-x-5 items-center">
            <input type="text" id="apiLink" class="w-1/2 px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="search" id="search" placeholder="Link">
            <button id="copy-button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Copy</button>
        </div>

        <div class="flex gap-x-10 items-center mt-5">
                @csrf
                <input type="hidden" name="access_token" id="access_token" value={{session('access_token')}}>
                <input type="hidden" name="api_link" id="api_link" value="http://localhost:8000/api/quiz?Language=1&Category=1&SubCategory=1&Subject=1&Topic=1">
                <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Deploy</button>
            <!-- <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 h-[100%] font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Export</button> -->
        </div>

    </form>

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
        const baseUrl = "{{ url('api/quiz') }}";
        console.log(baseUrl);
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

        const copyButton = document.getElementById('copy-button');
        copyButton.addEventListener('click', () => {
            apiLink.select();
            document.execCommand('copy');
        });
        
        $('#select_language').change(function() {
            var languageId = $(this).val();
            $('#select_category').empty().append('<option value="">Select Category</option>');
            $('#select_sub_category').empty().append('<option value="">Select Sub Category</option>');
            $('#select_subject').empty().append('<option value="">Select Subject</option>');
            $('#select_topic').empty().append('<option value="">Select Topic</option>');

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
                    url: '/get-questions-data/' + languageId + '/' + categoryId + '/' + subCategoryId,
                    method: 'GET',
                    success: function(data) {
                        var subjects = data.subjects;
                        var questionTable = 
                            `
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg p-5 my-5 w-1/3">
                                    
                            `;

                        subjects.forEach((subject, index) => {
                            console.log(subject);
                            questionTable += 
                            `
                                <table id="questions-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <tbody>
                                        <tr>
                                            <td class="p-2" data-column="total-questions">${subject.questions}</td>
                                            <td class="p-2" data-column="subject">${subject.name}</td>
                                            <td class="p-2 w-28" data-column="questions">
                                                <input id="questions_${index}" name="questions[${index}]" type="text" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="questions" id="questions" placeholder="questions"> 
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
                                                <input type="text" class="w-full px-4 py-2 mt-2 mb-2 text-base text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:focus:border-blue-600" name="sets" id="sets" placeholder="sets"> 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            `;

                        questionTable += '</div>';

                        $('#question-data').html(questionTable);
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