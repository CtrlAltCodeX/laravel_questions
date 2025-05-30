@extends('layouts.app')

@section('title', 'Course')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Course</h1>

    <div class="flex justify-end items-center gap-2">
       
        <button id="createButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Create
        </button>

        <input type="text" id="searchFilter" placeholder="Search Offers..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
    </div>

</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
   
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="offersTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">

                    <a href="{{ route('courses.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                    Banner
                </th>
                <th scope="col" class="px-6 py-3">
                    <a href="{{ route('courses.index', array_merge(request()->all(), ['sort' => 'language', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Language Name
                        @if ($sortColumn == 'language')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="px-6 py-3">
                    <a href="{{ route('courses.index', array_merge(request()->all(), ['sort' => 'category', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Category Name
                        @if ($sortColumn == 'category')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="px-6 py-3">
                    <a href="{{ route('courses.index', array_merge(request()->all(), ['sort' => 'sub_category', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Sub Category Name
                        @if ($sortColumn == 'sub_category')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="px-6 py-3">
                    <a href="{{ route('courses.index', array_merge(request()->all(), ['sort' => 'subject', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Subject Name
                        @if ($sortColumn == 'subject')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>

           
                <th scope="col" class="px-6 py-3">
                    <a href="{{ route('courses.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Course Name
                        @if ($sortColumn == 'name')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
               
                  <th scope="col" class="px-6 py-3">
                    Price
                </th>
                  <th scope="col" class="px-6 py-3">
                    Subscription 
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $course)
            <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">{{ $course->id }}</td>
                <td class="px-4 py-3 text-center">
              
              <img src="{{ $course->banner ? '/uploads/courses/'.$course->banner : '/dummy.jpg' }}" style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;' />

                </td>
                <td class="px-4 py-3 text-center">{{ $course->language_name }}</td>
                <td class="px-4 py-3 text-center">{{ $course->category_name }}</td>
                <td class="px-4 py-3 text-center">{{ $course->sub_category_names }}</td>
                <td class="px-4 py-3 text-center">{{ $course->subject_names }}</td>
                <td class="px-4 py-3 text-center">{{ $course->name }}</td>
                <td class="px-4 py-3 text-center">{{ $course->formatted_prices ?? '-' }}</td>
                <td class="px-4 py-3 text-center">{{ $course->subscription_names ?? '-' }}</td>
                <td class="px-4 py-3 text-center font-bold {{ $course->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $course->status == 1 ? 'Enabled' : 'Disabled' }}
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-3">
                        <button class="text-blue-600 hover:underline editButton"
                            data-id="{{ $course->id }}"
                            data-name="{{ $course->name }}"
                            data-language-id="{{ $course->language_id }}"
                            data-category-id="{{ $course->category_id }}"
                            data-subcategories='@json(json_decode($course->sub_category_id, true))'
                            data-subjects='@json(json_decode($course->subject_id, true))'
                            data-status="{{ $course->status }}"
                            data-banner="{{ $course->banner }}"
                            data-subscriptions='@json($course->subscription)'>
  <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>                        </button>
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">
                                  <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                            </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="px-4 py-4 text-center text-gray-500">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(request()->data != 'all')
    <div class="flex justify-between items-center">
        <div style="width: 92%;">
            {{ $courses->appends(request()->query())->links() }}

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
            <div class="relative" style="height: 100px;">
                <div class="container">
                    <input accept="image/*" type="file" class="opacity-0 w-[100] h-[100] absolute z-10 cursor-pointer" name="banner" style="width: 100px; height:100px;" id='fileInput' />
                    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white image" src="/dummy.jpg" alt="" id='offerImage' style='width:100px;height:100px;'>
                    <div class="bg-black/[0.5] overlay absolute h-[100%] top-[0px] w-[100px] rounded-full opacity-0 flex justify-center items-center text-white">Upload Pic</div>
                </div>
            </div>

            <div class="mb-3">
                <input type="text" id="name" placeholder="Course Name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="name"  />
                  <div style="color: red;" id="error-name"></div>  

            </div>
            <div class="mx-auto mb-3">
                <select id="select_language" name="language_id" class="select_language bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Select Language</option>
                    @foreach($languages as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
                             <div id="error-language_id" class="error-text" style="color: red;"></div>


            </div>

            <div class="mx-auto mb-3">
                <select id='select_category' name="category_id" class="select_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Choose a Category</option>
                    @foreach($categories as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                    @endforeach
                </select>
                    <div id="error-category_id" class="error-text" style="color: red;"></div>

            </div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Categories</label>
    <select id="subCategorySelect" name="subcategories[]" class="select_sub_category w-full border-gray-300 rounded-md" multiple>
        <option value="all">Select All</option>
        @foreach($subcategories as $sub)
            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Subjects</label>
    <select id="subjectSelect" name="subjects[]" multiple class="w-full border-gray-300 rounded-md">
    @foreach($subjects as $subject)
                    <option value="{{$subject->id}}">{{$subject->name}}</option>
                    @endforeach
</select>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Subscription Plans</label>

    <!-- Monthly -->
    <div class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="monthlyCheck" name="subscription[monthly][active]" value="1" class="subscriptionCheck" />
        <label for="monthlyCheck" class="w-24">Monthly</label>
        <input type="number" name="subscription[monthly][amount]" placeholder="Amount" class="border border-gray-300 rounded p-1 w-28" />
        <input type="number" name="subscription[monthly][validity]" placeholder="Validity " class="border border-gray-300 rounded p-1 w-36" />
    </div>

    <!-- Semi Annual -->
    <div class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="semiAnnualCheck" name="subscription[semi_annual][active]" value="1" class="subscriptionCheck" />
        <label for="semiAnnualCheck" class="w-24">Semi Annual</label>
        <input type="number" name="subscription[semi_annual][amount]" placeholder="Amount" class="border border-gray-300 rounded p-1 w-28" />
        <input type="number" name="subscription[semi_annual][validity]" placeholder="Validity " class="border border-gray-300 rounded p-1 w-36" />
    </div>

    <!-- Annual -->
    <div class="flex items-center gap-2">
        <input type="checkbox" id="annualCheck" name="subscription[annual][active]" value="1" class="subscriptionCheck" />
        <label for="annualCheck" class="w-24">Annual</label>
        <input type="number" name="subscription[annual][amount]" placeholder="Amount" class="border border-gray-300 rounded p-1 w-28" />
        <input type="number" name="subscription[annual][validity]" placeholder="Validity " class="border border-gray-300 rounded p-1 w-36" />
    </div>
</div>




<div class="mb-3">
  
    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        <option value="">Select Status</option>
        <option value="1">Enabled</option>
        <option value="0">Disabled</option>
    </select>
                 <div id="error-status" class="error-text" style="color: red;"></div>

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
    $(document).ready(function () {
     

        // Select All Logic
        $('#subCategorySelect').on('change', function (e) {
            let selected = $(this).val();
            let allValues = $('#subCategorySelect option').map(function () {
                return $(this).val();
            }).get();

            if (selected.includes("all")) {
                // Remove "all" and select everything else
                $(this).val(allValues.filter(val => val !== "all")).trigger('change');
                            $('#subCategorySelect').val(allValues).trigger('change');

            }
        });
    });
</script>


<script>


    document.getElementById('searchFilter').addEventListener('input', function () {
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
                var output = document.getElementById('offerImage');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });


</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('createButton').addEventListener('click', function() {
            // Reset fields for creating a new subcategory
            document.getElementById('modalTitle').innerText = 'Create Course';
            document.getElementById('modalForm').action = "{{ route('courses.store') }}";
            document.getElementById('modalForm').method = 'POST';
            document.getElementById('modalForm').querySelector('input[name="_method"]').value = '';
            document.getElementById('name').value = '';
            document.getElementById('select_language').value = '';
            document.getElementById('select_category').value = '';
         
            document.getElementById('offerImage').src = '/dummy.jpg';
            document.getElementById('modal').style.display = 'flex';
        });

  const editButtons = document.querySelectorAll('.editButton');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const languageId = this.getAttribute('data-language-id');
                const categoryId = this.getAttribute('data-category-id');
             const subcategories = JSON.parse(this.dataset.subcategories || '[]');
        const subjects = JSON.parse(this.dataset.subjects || '[]');
                const status = this.getAttribute('data-status');
                const banner = this.getAttribute('data-banner');
                const subscriptions = JSON.parse(this.getAttribute('data-subscriptions') || '{}');

                // Fill modal fields
                document.getElementById('modalTitle').innerText = 'Edit Course';
                document.getElementById('modalForm').action = `/courses/${id}`;
                // document.querySelector('input[name="_method"]').value = 'PUT';
                 document.getElementById('modalForm').method = 'POST';
                document.getElementById('modalForm').querySelector('input[name="_method"]').value = 'PUT';

                document.getElementById('name').value = name || '';
                document.getElementById('select_language').value = languageId || '';
                document.getElementById('select_category').value = categoryId || '';

               // Set subcategories
        const subSelect = document.querySelector('#subCategorySelect');
        Array.from(subSelect.options).forEach(opt => {
            opt.selected = subcategories.includes(opt.value) || subcategories.includes(parseInt(opt.value));
        });

        // Set subjects
        const subjectSelect = document.querySelector('#subjectSelect');
        Array.from(subjectSelect.options).forEach(opt => {
            opt.selected = subjects.includes(opt.value) || subjects.includes(parseInt(opt.value));
        });
    

              // Monthly
document.getElementById('monthlyCheck').checked = !!subscriptions.monthly;
document.querySelector('input[name="subscription[monthly][amount]"]').value = subscriptions.monthly?.amount || '';
document.querySelector('input[name="subscription[monthly][validity]"]').value = subscriptions.monthly?.validity || '';

// Semi Annual
document.getElementById('semiAnnualCheck').checked = !!subscriptions.semi_annual;
document.querySelector('input[name="subscription[semi_annual][amount]"]').value = subscriptions.semi_annual?.amount || '';
document.querySelector('input[name="subscription[semi_annual][validity]"]').value = subscriptions.semi_annual?.validity || '';

// Annual
document.getElementById('annualCheck').checked = !!subscriptions.annual;
document.querySelector('input[name="subscription[annual][amount]"]').value = subscriptions.annual?.amount || '';
document.querySelector('input[name="subscription[annual][validity]"]').value = subscriptions.annual?.validity || '';

                document.getElementById('status').value = status || '';
                document.getElementById('offerImage').src = banner ? `/uploads/courses/${banner}` : '/dummy.jpg';

                // Show modal
                document.getElementById('modal').style.display = 'flex';
            });
        });

  


    });
</script>
<script>
    function handleSelectAll(selectEl) {
        const selected = Array.from(selectEl.selectedOptions).map(opt => opt.value);
        const isAllSelected = selected.includes('all');

        if (isAllSelected) {
            // Select all except "all"
            for (let option of selectEl.options) {
                if (option.value !== 'all') option.selected = true;
            }
        } else if (!selected.length) {
            // Clear all
            for (let option of selectEl.options) {
                option.selected = false;
            }
        }
    }

    document.getElementById('subCategorySelect').addEventListener('change', function () {
        handleSelectAll(this);

        const selected = Array.from(this.selectedOptions)
            .map(opt => opt.value)
            .filter(val => val !== 'all'); // exclude "all" from request

        if (selected.length > 0) {
            fetch(`/get-subjects?ids=${selected.join(',')}`)
                .then(res => res.json())
                .then(data => {
                    const subjectSelect = document.getElementById('subjectSelect');
                    subjectSelect.innerHTML = '<option value="all">Select All</option>'; // reset and add 'all'

                    data.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.name;
                        subjectSelect.appendChild(option);
                    });
                });
        } else {
            document.getElementById('subjectSelect').innerHTML = '<option value="all">Select All</option>';
        }
    });

    document.getElementById('subjectSelect').addEventListener('change', function () {
        handleSelectAll(this);
    });
</script>

@endpush