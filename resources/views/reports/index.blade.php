@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Reports</h1>

    <div class="flex justify-end items-center gap-2">



        <input type="text" id="searchFilter" placeholder="Search Reports..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
    </div>

</div>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
    <form action="{{ route('reports.index') }}" method="GET">

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="offersTable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Id
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Message
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Date
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr class=" offerRow odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$report->id}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$report->name}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$report->title}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$report->type}}
                    </th>


                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$report->message}}
                    </th>

                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$report->date}}
                    </th>
                    <td class="px-6 py-4 flex gap-4">
                        @if ($report->type === 'Video')
                        <button class="edit-report"
                            data-id="{{ $report->video_id }}"
                            data-reportid="{{ $report->id }}"
                            data-type="{{ $report->type }}">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                            </svg>
                        </button>
                        @else
                        <button class="open-edit-modal"
                            data-id="{{ $report->question_id }}"
                            data-reportid="{{ $report->id }}"
                            data-type="{{ $report->type }}">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                            </svg>
                        </button>
                        @endif
                        <form action="{{ route('reports.destroy', $report->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-danger dark:text-danger-500 hover:underline" onclick="return confirm('Are you sure?')">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                    <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                                </svg>
                            </button>
                        </form>

                    </td>




                </tr>
                @empty
                <tr>
                    <td colspan="7" align="center">No Result Found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if(request()->data != 'all')
        <div class="flex justify-between items-center">
            <div style="width: 92%;">
                {{ $reports->appends(request()->query())->links() }}

            </div>

            <div>
                <a href="{{ request()->fullUrlWithQuery(['data' => 'all']) }}"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View All
                </a>
            </div>
        </div>
        @endif
</div>

@endsection

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
        overflow-y: auto; /* Make content scrollable if it exceeds max height */
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Modal Title</h2>
            <button id="closeModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>
        <form id="modalForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="">
            <div class="mb-3 relative" style="height: 100px;">
                <div class="container">
                    <input accept="image/*" type="file" class="opacity-0 w-[100] h-[100] absolute z-10 cursor-pointer" name="thumbnail" style="width: 100px; height:100px;" id='fileInput' />
                    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white image" src="/dummy.jpg" alt="" id='VideoImage' style='width:100px;height:100px;'>
                    <div class="bg-black/[0.5] overlay absolute h-[100%] top-[0px] w-[100px] rounded-full opacity-0 flex justify-center items-center text-white">Upload Pic</div>
                </div>
            </div>

            <div class="mb-3">
                <input type="text" id="name" placeholder="Video Title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="name" required />
                @error('name')
                <div class="text-danger">{{ $message }}</div>
                @enderror
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
                    @foreach($categories as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>



            <div class="mx-auto mb-3">
                <select id='select_sub_category' name="sub_category_id" class="select_sub_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Sub Category</option>
                    @foreach($subcategories as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="mx-auto mb-3">
                <select id='select_subject' name="subject_id" class="select_subject bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Subject</option>
                    @foreach($subjects as $subject)
                    <option value="{{$subject->id}}">{{$subject->name}}</option>
                    @endforeach
                </select>
                @error('subject_id')
                <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mx-auto mb-3">
                <select id='select_topic' name="topic_id" class="select_topic bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Topic</option>
                    @foreach($topics as $topic)
                    <option value="{{$topic->id}}">{{$topic->name}}</option>
                    @endforeach
                </select>
                @error('topic_id')
                <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <!-- Discount & Valid Until -->
            <div class="flex justify-between mb-3">
                <input type="text" id="v_no" placeholder="V.N." name="v_no" style="margin-right: 10px;"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
                    required />

                <input type="text" placeholder="Description" id="description" name="description"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
                    required />
            </div>

            <div class="flex justify-between mb-3">
                <input type="text" id="youtube_link" placeholder="Youtube Link" name="youtube_link" style="margin-right: 10px;"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
                    required />

                <input type="text" placeholder="Upload/Video Id" id="video_id" name="video_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
                    required />

                <input type="text" placeholder="pdf_link" id="pdf_link" name="pdf_link"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
                    required />
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




<!-- here question model page  -->

<div id="editModal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="
        background-color: white; 
        border-radius: 10px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        width: 55%; 
        max-height: 90vh; /* Maximum height to keep it within the viewport */
        margin: auto; 
        padding: 24px; 
        position: relative; 
        overflow-y: auto; /* Make content scrollable if it exceeds max height */
    ">
        <div style="display: flex; justify-content: end; align-items: center; margin-bottom: 16px;">
            <button id="closeEditModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>
        <form id="questioneditForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Edit Question</h1>
            <div id="modalContent">
                <!-- Fields will be dynamically injected here -->
            </div>

            <div class="relative overflow-x-auto sm:rounded-lg">
                <div id="input-rows"></div>
                <div class="flex justify-end gap-x-5 mt-5">
                    <button type="submit" id="Update-btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>



@push('scripts')

@include('script')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.status-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    if (cb !== this) {
                        cb.checked = false; // Ek time pe sirf ek select ho
                    }
                });
            });
        });
    });

    document.getElementById('searchFilter').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#offersTable .offerRow');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });


    document.getElementById('fileInput').addEventListener('change', function(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('VideoImage');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        $(document).on("click", ".edit-report", function(e) {
            e.preventDefault(); // Prevent default anchor behavior

            var id = $(this).data("id");
            var type = $(this).data("type");
            const report_id = this.getAttribute('data-reportid');

            if (type === "Video") {

                $.ajax({
                    url: "{{ route('reports.edit') }}", // Ensure it's wrapped in quotes
                    type: "GET",
                    data: {
                        type: type,
                        id: id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }


                        const data = response?.data;

                        // Extract nested fields correctly
                        const languageId = data?.topic?.subject?.sub_category?.category?.language?.id || "";
                        const categoryId = data?.topic?.subject?.sub_category?.category?.id || "";
                        const subCategoryId = data?.topic?.subject?.sub_category?.id || "";
                        const subjectId = data?.topic?.subject?.id || "";
                        const topicId = data?.topic?.id || "";

                        // Set modal fields
                        document.getElementById('modalTitle').innerText = 'Edit Video';
                        document.getElementById('modalForm').action = `/reports/update/${id}/${report_id}`;
                        document.getElementById('modalForm').method = 'POST';
                        document.getElementById('modalForm').querySelector('input[name="_method"]').value = 'PUT';

                        document.getElementById('name').value = data?.name;
                        document.getElementById('select_language').value = languageId;
                        document.getElementById('select_category').value = categoryId;
                        document.getElementById('select_sub_category').value = subCategoryId;
                        document.getElementById('select_subject').value = subjectId;
                        document.getElementById('select_topic').value = topicId;
                        document.getElementById('v_no').value = data.v_no;
                        document.getElementById('description').value = data.description;
                        document.getElementById('youtube_link').value = data.youtube_link;
                        document.getElementById('video_id').value = data.video_id;
                        document.getElementById('pdf_link').value = data.pdf_link;
                        document.getElementById('video_type').value = data.video_type;

                        // Handle thumbnail image
                        document.getElementById('VideoImage').src = data.thumbnail ? `/storage/${data.thumbnail}` : '/dummy.jpg';

                        // Handling video type checkboxes

                        document.getElementById('modal').style.display = 'flex';
                        // Show modal
                    },
                    error: function(xhr) {
                        console.error("Error fetching data:", xhr.responseText);
                        alert("An error occurred while fetching the data.");
                    }
                });
            } else {
                console.log("question" + id + type + report_id)
            }
        });


        $(document).on("click", ".open-edit-modal", function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            document.getElementById('editModal').style.display = 'flex';
        });



        // Close modal button
        $("#closeModal").click(function() {
            $("#modal").hide();
        });

    });
</script>

<script>
    $(document).ready(function() {
        const editQuestionRoute = "{{ route('question.edit', ':id') }}"; // Define the route here
        var quillQuestion = '';

        document.querySelectorAll('.open-edit-modal').forEach(button => {
            button.addEventListener('click', function() {
                const questionId = this.dataset.id;

                const report_id = this.getAttribute('data-reportid');

                console.log("question" + questionId + report_id)
                // Fetch data from the server
                const fetchUrl = editQuestionRoute.replace(':id', questionId);

                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate modal with data
                        console.log("edit data question data", data);
                        const modalContent = document.getElementById('modalContent');
                        document.getElementById('questioneditForm').action = `/reports/updateQuestion/${questionId}/${report_id}`;
                        document.getElementById('questioneditForm').method = 'POST';
                        document.getElementById('questioneditForm').querySelector('input[name="_method"]').value = 'PUT';
                        // Clear previous content
                        modalContent.innerHTML = '';

                        // Populate question ID field (if needed)
                        const questionIdField = document.createElement('input');
                        questionIdField.type = 'hidden';
                        questionIdField.name = 'id';
                        questionIdField.value = data.question.id;
                        modalContent.appendChild(questionIdField);

                        // Populate dropdowns
                        for (const [moduleName, module] of Object.entries(data.dropdown_list)) {
                            const idMapping = {
                                'Select Language': 'language_id',
                                'Select Category': 'category_id',
                                'Select Sub Category': 'sub_category_id',
                                'Select Subject': 'subject_id',
                                'Select Topic': 'topic_id',
                            };

                            // Map the moduleName to the corresponding key in data.question
                            const questionKey = idMapping[moduleName];
                            const selectedId = data.question[questionKey];

                            const id = moduleName.toLowerCase().replace(/\s+/g, '_');

                            const selectField = document.createElement('div');
                            selectField.className = 'mb-4';
                            selectField.innerHTML = `
                                <label for="${id}" class="block text-sm font-medium">${moduleName}</label>
                                <select id="${id}" name="module[${id}][]" class="${id} block w-full p-2 border rounded required" >
                                    <option value="">${moduleName}</option>
                                    ${module.map(item => `
                                        <option value="${item.id}" ${item.id === selectedId ? 'selected' : ''}>${item.name}</option>
                                    `).join('')}
                                </select>
                            `;
                            modalContent.appendChild(selectField);
                        }
                        const questionNumberField = document.createElement('div');
                        questionNumberField.className = 'mb-4';
                        questionNumberField.innerHTML = `
                            <label for="qno" class="block text-sm font-medium">Question No.</label>
                            <input id="qno" type="text" name="qno[]" class="block w-full p-2 border rounded required"
                                value="${data.question.question_number || ''}" placeholder="Question No." />
                        `;
                        modalContent.appendChild(questionNumberField);

                        // Add Photo Link
                        const photoLinkField = document.createElement('div');
                        photoLinkField.className = 'mb-4';
                        photoLinkField.innerHTML = `
                            <label for="photo_link" class="block text-sm font-medium">Photo Link</label>
                            <input id="photo_link" type="text" name="photo_link" class="block w-full p-2 border rounded"
                                value="${data.question.photo_link || ''}" placeholder="Photo Link" />
                        `;
                        modalContent.appendChild(photoLinkField);

                        // Function to dynamically create the photo upload field
                        const uploadPhotoField = document.createElement('div');
                        uploadPhotoField.className = 'flex justify-between w-full mb-2';
                        uploadPhotoField.innerHTML = `
                            <div class='relative'>
                                <label for="fileInput-new" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Photo</label>
                                <button class="remove-image" style='background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;position: absolute;top: 20px;left: 110px;z-index:10;'>X</button>
                                <input type="hidden" id="photo-${data.question.id}" name="photo" value="${data.question.photo}" />
                                <input type="file" accept="image/*" name="photo" style="height: 155px;" class="file-input absolute inset-0 w-full h-full opacity-0 cursor-pointer fileInput" id="fileInput${data.question.id}" />
                                <div class="image-container" style="height: 155px;">
                                    <img id="imagePreview${data.question.id}" class="h-full object-cover rounded-lg imagepreview"
                                        src="${data.question.photo ? '/storage/questions/'+data.question.photo : '/dummy.jpg'}" alt="Image Preview" width="150" />
                                </div>
                                <button type="button" id="fileButton${data.question.id}" class="absolute top-[0px] z-[-1] custom-file-button bg-gray-50 w-full h-full border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    Upload Photo
                                </button>
                            </div>
                        `;

                        // Append the field to the modal content
                        modalContent.appendChild(uploadPhotoField);

                        const questionField = document.createElement('div');
                        questionField.className = 'mb-4';
                        questionField.style.height = '200px';
                        questionField.innerHTML = `
                            <label for="questionText" class="block text-sm font-medium">Question</label>
                            <div id="questionText" class="editor-question required bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 h-50">
                            ${data.question?.question || ''}
                            </div>
                            <input type="hidden" name="question[]" class="question-input-edit required" value="${data.question?.question || ''}" />
                        `;
                        modalContent.appendChild(questionField);

                        quillQuestion = new Quill('#questionText', {
                            theme: 'snow'
                        });

                        $('#Update-btn').on('click', function() {
                            $('.question-input-edit').val(quillQuestion.root.innerHTML);
                        });

                        const optionsContainer = document.createElement('div');
                        optionsContainer.className = 'grid grid-cols-2 gap-4';
                        ['A', 'B', 'C', 'D'].forEach(option => {
                            optionsContainer.innerHTML += `
                            <div>
                                <label for="option_${option}" class="block text-sm font-medium">Option ${option}</label>
                                <input id="option_${option}" name="option_${option.toLowerCase()}" type="text"
                                    class="block w-full p-2 border rounded required"
                                    value="${data.question[`option_${option.toLowerCase()}`] || ''}">
                            </div>
                        `;
                        });
                        modalContent.appendChild(optionsContainer);

                        const answerField = document.createElement('div');
                        answerField.className = 'mb-4';
                        answerField.innerHTML = `
                            <label for="answer" class="block text-sm font-medium">Answer</label>
                            <select id="answer" name="answer" class="block w-full p-2 border rounded required">
                                <option value="">Select Answer</option>
                                ${['A', 'B', 'C', 'D'].map(option => `
                                    <option value="${option}" ${data.question?.answer === option ? 'selected' : ''}>${option}</option>
                                `).join('')}
                            </select>
                        `;
                        modalContent.appendChild(answerField);


                        // Add the "Level" dropdown
                        const levelField = document.createElement('div');
                        levelField.className = 'mb-4';
                        levelField.innerHTML = `
                        <label for="level" class="block text-sm font-medium">Level</label>
                        <select class='block w-full p-2 border rounded' name='level'>
                        <option>Select</option>
                        <option value=1 ${data.question?.level === '1' ? 'selected' : ''}>Easy</option>
                        <option value=2 ${data.question?.level === '2' ? 'selected' : ''}>Medium</option>
                        <option value=3 ${data.question?.level === '3' ? 'selected' : ''}>Hard</option>
                        </select>
                        `;
                        modalContent.appendChild(levelField);


                        // Add the "Notes" textarea
                        const notesField = document.createElement('div');
                        notesField.className = 'w-full';
                        notesField.innerHTML = `
                            <label for="notes" class="block text-sm font-medium">Notes</label>
                            <textarea id="notes" name="notes[]" rows="3" class="block w-full p-2 border rounded" placeholder="Notes">${data.question.notes??''}</textarea>
                        `;
                        modalContent.appendChild(notesField);
                        document.getElementById('editModal').style.display = 'flex';
                    })
                    .catch(error => console.error('Error fetching question data:', error));
            });

        });

        document.getElementById('questioneditForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const actionUrl = this.action;
            const requiredFields = this.querySelectorAll('.required');
            let allFieldsFilled = true;
            let missingFields = [];

            // Validate required fields
            requiredFields.forEach(field => {
                if (['INPUT', 'TEXTAREA', 'SELECT'].includes(field.tagName)) {
                    // If it's an input, textarea, or select, check its value
                    if (!field.value.trim()) {
                        allFieldsFilled = false;
                        missingFields.push(field.name || field.id || 'Unnamed field');
                    }
                }
            });

            let content = quillQuestion.getText().trim();
            if (content.length === 0) {
                alert('Question field is required.');
                e.preventDefault(); // Prevent form submission
                return;
            }

            if (!allFieldsFilled) {
                alert(`Please fill in all required fields: ${missingFields.join(', ')}`);
                return;
            }

            // for (let [key, value] of formData.entries()) {
            //     console.log(`${key}: ${value}`);
            // }

            setTimeout(() => {
                fetch(actionUrl, {
                        method: this.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            // Handle validation errors if any
                            console.error(data.errors);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }, 500)
        });

        document.getElementById('closeEditModal').addEventListener('click', function() {
            document.getElementById('editModal').style.display = 'none';
        });






    });
</script>



@endpush