@extends('layouts.app')

@section('title', 'Videos')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Videos</h1>

    <div class="flex flex-col items-self-end gap-2">
        <div class="flex justify-end items-center gap-2">
            <form action="{{ route('videos.index') }}" method="GET" id='data' class="mb-0 flex gap-2">
                @foreach ($dropdown_list as $moduleName => $module)
                @php
                $id = strtolower(Str::slug($moduleName, '_'));
                $moduleKey = Str::slug(strtolower(trim(explode('Select', $moduleName)[1])) . "_id", '_');
                $selectedValue = request()->input($moduleKey);
                @endphp
                <div>
                    <select name="{{ $moduleKey }}" id='{{ $id }}_select' class="{{ $id }} bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required-field" style="width: 160px;">
                        <option value="">{{$moduleName}}</option>
                        @foreach($module as $item)
                        <option value="{{$item->id}}" {{ $selectedValue == $item->id ?  'selected' : '' }}>{{$item->name}}</option>
                        @endforeach
                    </select>
                    <div class="text-red-500 text-xs mt-1 validation-msg"></div>
                    @error('module.' . $moduleKey)
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach
        
                <button id="filter-btn" type="submit" class="text-white text-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Filter</button>
            </form>
        </div>
    </div>

</div>

<div class="flex gap-2 justify-content-end">
    <button id="createButton" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none">
        Create
    </button>
    <input type='file' name='file' id='importInput' class='hidden' />
    <button id='importButton' class='text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none'>Import</button>
    <a href='{{ route("videos.export") }}' class='text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none'>Export</a>
    <!-- <input type="text" id="searchFilter" placeholder="Search Videos..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white"> -->
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="offersTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">

                    <a href="{{ route('videos.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        #
                        @if ($sortColumn == 'id')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>

                <th scope="col" class="px-6 py-3">
                    V.N.
                </th>
                <th scope="col" class="px-6 py-3">
                    Thumbnail/
                    Video Title
                </th>
                <th scope="col" class="px-6 py-3">
                    <a href="#">
                        Language Name / Category Name / Sub Category Name / Subject Name / Topic Name
                    </a>
                </th>

                <th scope="col" class="px-6 py-3">
                    Description
                </th>

                <th scope="col" class="px-6 py-3">
                    YouTube Link
                </th>

                <th scope="col" class="px-6 py-3">
                    Video Type
                </th>
              
              	<th scope="col" class="px-6 py-3">
                    External Link
                </th>

                <th scope="col" class="px-6 py-3">
                    Pdf
                </th>

                <th scope="col" class="px-6 py-3">
                    action
                </th>

            </tr>
        </thead>
        <tbody>
            @forelse($videos as $video)
            <tr class="offerRow odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$video->id}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$video->v_no}}
                </th>

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white flex justify-center flex-wrap flex-col items-center">
                    <img class='mb-2' src="{{ $video->thumbnail ? '/storage/'.$video->thumbnail : '/dummy.jpg'}}" style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;' />
                    {{$video->name}}
                </th>

                @php
                    $fullText = $video->topic->subject->subCategory->category->language->name . ' / ' .
                                $video->topic->subject->subCategory->category->name . ' / ' .
                                $video->topic->subject->subCategory->name . ' / ' .
                                $video->topic->subject->name . ' / ' .
                                $video->topic->name;

                    // limit to 50 words
                    $shortText = Str::words($fullText, 10, '...');
                @endphp

                <th scope="row"
                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white relative group">
                    <span>{{ $shortText }}</span>
                    <div class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded px-3 py-2 w-max max-w-fit z-10 -top-2 left-1/2 -translate-x-1/2">
                        {{ $fullText }}
                    </div>
                </th>
                {{-- <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"></th> --}}
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$video->description}}
                </th>
                {{-- <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <a href="{{ $video->youtube_link }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ $video->youtube_link }}
                    </a>
                </th> --}}
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{-- <a href="#" target="_blank" class="text-blue-500 hover:underline"> --}}
                        {{ $video->video_id }}
                    {{-- </a> --}}
                </th>

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$video->video_type}}
                </th>
              
              	<th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    @php
                        $videoLink = "";
                        $viewBtn = '-';
                        if($video->video_link) {
                            $videoLink = Storage::disk('minio')->temporaryUrl($video->video_link, now()->addMinutes(30));
                            $viewBtn = 'View';
                        }
                    @endphp
                    <a href="{{ $videoLink }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ $viewBtn }}
                    </a>
                </th>

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
	                 @php
                        $pdfLink = "";
                        $viewBtn = '-';
                        if($video->pdf_link) {
                            $pdfLink = Storage::disk('minio')->temporaryUrl($video->pdf_link, now()->addMinutes(30));
                            $viewBtn = 'View';
                        }
                    @endphp
                    <a href="{{ $pdfLink }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ $viewBtn }}
                    </a>
                </th>

                <td class="px-6 py-4 flex gap-4">
                    <button class="editvieoButton font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        data-id="{{ $video->id }}"
                        data-name="{{ $video->name }}"
                        data-language-id="{{$video->topic->subject->subCategory->category->language->id }}"
                        data-category-id="{{$video->topic->subject->subCategory->category->id }}"
                        data-sub-category-id="{{$video->topic->subject->subCategory->id }}"
                        data-subject-id="{{$video->topic->subject->id }}"
                        data-topic-id="{{$video->topic->id }}"
                        data-video_type="{{ $video->video_type }}"
                        data-v_no-id="{{$video->v_no }}"
                        data-description="{{$video->description }}"
                        data-youtube_link="{{$video->youtube_link }}"
                        data-video_id="{{$video->video_id }}"
                        data-pdf_link="{{$video->pdf_link }}"
                        data-thumbnail="{{$video->thumbnail }}">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('videos.destroy', $video->id) }}" method='POST'>
                        @csrf
                        @method('DELETE')
                        <button href="#" class="font-medium text-danger dark:text-danger-500 hover:underline" onclick="return confirm('Are you sure? ')">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" align="center">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(request()->data != 'all')
<div class="flex justify-between items-center">
    <div style="width: 92%;">
        {{ $videos->appends(request()->query())->links() }}
    </div>

    <div>
        <a href="{{ request()->fullUrlWithQuery(['data' => 'all']) }}"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            View All
        </a>
    </div>
</div>
</div>
@endif

<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="
        background-color: white; 
        border-radius: 10px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        width: 30%; 
        max-height: 90vh; /* Maximum height to keep it within the viewport */
        margin: auto; 
        padding: 24px; 
        position: relative; 
        overflow-y: auto;
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Modal Title</h2>
            <button id="closeModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>

        <form id="modalForm" method="POST" action="" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
            <div style="color: red; margin-bottom: 10px;">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <input type="hidden" name="_method" value="">
            <div class="mb-3 relative" style="height: 100px;">
                <div class="container">
                    <input accept="image/*" type="file" class="opacity-0 w-[100] h-[100] absolute z-10 cursor-pointer" name="thumbnail" style="width: 100px; height:100px;" id='fileInput' />
                    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white image" src="/dummy.jpg" alt="" id='VideoImage' style='width:100px;height:100px;'>
                    <div class="bg-black/[0.5] overlay absolute h-[100%] top-[0px] w-[100px] rounded-full opacity-0 flex justify-center items-center text-white">Upload Pic</div>
                </div>
            </div>
            
            <div class='mb-3'>
                <input type="text" id="v_no" placeholder="V.N." name="v_no" style="margin-right: 10px;"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required />
            </div>

            <div class="mb-3">
                <input type="text" id="name" placeholder="Video Title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="name" required />
                @error('name')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class='mb-3'>
                <input type="text" placeholder="Description" id="description" name="description"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required />
            </div>
            
            <div class="mx-auto mb-3">
                <select id="select_language" name="language_id" class="select_language bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Select Language</option>
                    @foreach($languages as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
                @error('language_id')
                <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mx-auto mb-3">
                <select id='select_category' name="category_id" class="select_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Category</option>

                </select>
            </div>

            <div class="mx-auto mb-3">
                <select id='select_sub_category' name="sub_category_id" class="select_sub_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Sub Category</option>

                </select>
            </div>

            <div class="mx-auto mb-3">
                <select id='select_subject' name="subject_id" class="select_subject bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Subject</option>

                </select>
                @error('subject_id')
                <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mx-auto mb-3">
                <select id='select_topic' name="topic_id" class="select_topic bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Topic</option>

                </select>
                @error('topic_id')
                <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class='mb-3'>
                <input type="text" placeholder="Youtube Link" id="video_id" name="youtube_link"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 w-full"
                     />
            </div>

            <div class='mb-3'>
                <label>Video Upload</label>
                <input type="file" name="video" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />

                <div id="error-video" class="text-red-500 mt-1"></div>
            </div>

            <div class='mb-3'>
                <label>PDF Upload</label>
                <input type="file" accept="application/pdf" id="pdf_input" name="pdf_link" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>

            <div class="mb-3 max-auto">
                <select id="video_type" name="video_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Select Type</option>
                    <option value="Free">Free</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <button type="submit" style="background-color: #2563EB; color: white; font-size: 14px; font-weight: 500; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;">
                Save
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')

@include('script')

<script>
    document.getElementById('pdf_input').addEventListener('change', function(event) {
        let file = event.target.files[0];
        if (file) {
            document.getElementById('pdf_filename').value = file.name;
        }
    });

    // document.getElementById('searchFilter').addEventListener('input', function() {
    //     let filter = this.value.toLowerCase();
    //     let rows = document.querySelectorAll('#offersTable .offerRow');

    //     rows.forEach(row => {
    //         let text = row.textContent.toLowerCase();
    //         row.style.display = text.includes(filter) ? '' : 'none';
    //     });
    // });

    document.getElementById('fileInput').addEventListener('change', function(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('offerImage');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    $('#importButton').click(function() {
        $('#importInput').click();

        $('#importInput').change(function() {
            var form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("videos.import") }}',
                'enctype': 'multipart/form-data'
            });

            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));

            form.append($(this));

            form.appendTo('body').submit();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('createButton').addEventListener('click', function() {
          const modal = document.getElementById('modal');
          const form = document.getElementById('modalForm');

          // Reset title, form action, and method
          document.getElementById('modalTitle').innerText = 'Create Video';
          form.action = "{{ route('videos.store') }}";
          form.method = 'POST';

          const methodInput = form.querySelector('input[name="_method"]');
          if (methodInput) methodInput.value = '';

          // Reset all form fields
          form.reset();

          // Manually reset select dropdowns (if they are custom or dynamic)
          const selects = form.querySelectorAll('select');
          selects.forEach(select => {
              select.value = '';
              // Trigger change if needed (for JS plugins or custom UI)
              select.dispatchEvent(new Event('change'));
          });

          // Reset image preview
          const img = document.getElementById('VideoImage');
          if (img) img.src = '/dummy.jpg';

          // Clear error messages (if any)
          const errorFields = form.querySelectorAll('[id^="error-"]');
          errorFields.forEach(error => error.innerText = '');

          // Show modal
          modal.style.display = 'flex';
      });

        const editButtons = document.querySelectorAll('.editvieoButton');
        editButtons.forEach(button => {
            button.addEventListener('click', async function() {
                $('#form-loader').removeClass('hidden');
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const languageId = this.getAttribute('data-language-id');
                const categoryId = this.getAttribute('data-category-id');
                const subCategoryId = this.getAttribute('data-sub-category-id');
                const subjectId = this.getAttribute('data-subject-id');
                const topicId = this.getAttribute('data-topic-id');
                const v_no = this.getAttribute('data-v_no-id');
                const description = this.getAttribute('data-description');
                const video_type = this.getAttribute('data-video_type');

                const youtube_link = this.getAttribute('data-youtube_link');
                const video_id = this.getAttribute('data-video_id');
                const pdf_link = this.getAttribute('data-pdf_link');
                const thumbnail = this.getAttribute('data-thumbnail');

                await getCategories(languageId);
                await getSubCategories(categoryId);
                await getSubjects(subCategoryId);
                await getTopics(subjectId);

                // Set modal for editing a subcategory
                document.getElementById('modalTitle').innerText = 'Edit Video';
                document.getElementById('modalForm').action = `/videos/${id}`;
                document.getElementById('modalForm').method = 'POST';
                document.getElementById('modalForm').querySelector('input[name="_method"]').value = 'PUT';
                document.getElementById('name').value = name;
                document.getElementById('select_language').value = languageId;
                document.getElementById('select_category').value = categoryId;
                document.getElementById('select_sub_category').value = subCategoryId;
                document.getElementById('select_subject').value = subjectId;
                document.getElementById('select_topic').value = topicId;
                document.getElementById('v_no').value = v_no;
                document.getElementById('description').value = description;
                document.getElementById('video_id').value = video_id;

                document.getElementById('video_type').value = video_type;
                document.getElementById('VideoImage').src = thumbnail ? `/storage/${thumbnail}` : '/dummy.jpg';

                document.getElementById('modal').style.display = 'flex';
                $('#form-loader').addClass('hidden');
            });
        });
    });
</script>

@endpush