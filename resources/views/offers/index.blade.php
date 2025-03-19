@extends('layouts.app')

@section('title', 'Offers')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Offers</h1>

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

                    <a href="{{ route('offers.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                    <a href="{{ route('offers.index', array_merge(request()->all(), ['sort' => 'language', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                    <a href="{{ route('offers.index', array_merge(request()->all(), ['sort' => 'category', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                    <a href="{{ route('offers.index', array_merge(request()->all(), ['sort' => 'sub_category', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                    <a href="{{ route('offers.index', array_merge(request()->all(), ['sort' => 'subject', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
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
                    <a href="{{ route('offers.index', array_merge(request()->all(), ['sort' => 'topic_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        Offer Name
                        @if ($sortColumn == 'topic_name')
                        @if ($sortDirection == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                <th scope="col" class="px-6 py-3">
                    Discount
                </th>
                <th scope="col" class="px-6 py-3">
                    Validity
                </th>
                <th scope="col" class="px-6 py-3">
                    Mode
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
            @forelse($offers as $offer)
            <tr class="offerRow odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->id}}
                </th>
                
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <img src="{{ $offer->banner ? '/storage/'.$offer->banner : '/dummy.jpg'}}" style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;' />
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->subject->subCategory->category->language->name}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->subject->subCategory->category->name}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->subject->subCategory->name}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->subject->name}}
                </th>
           

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->name}}
                </th>

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->discount}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$offer->valid_until}}
                </th>
                <th class="px-6 py-4 font-medium 
    {{ $offer->mode == 'Upgrade Plan' ? 'text-blue-600' : 'text-black-600' }}">
    {{$offer->mode}}
</th>

                <th class="px-6 py-4 font-medium 
    {{ $offer->status == 1 ? 'text-blue-600' : 'text-red-600' }}">
    {{ $offer->status == 1 ? 'Enabled' : 'Disabled' }}
</th>


                <td class="px-6 py-4 flex gap-4">
                    <button class="editButton font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        data-id="{{ $offer->id }}"
                        data-name="{{ $offer->name }}"
                        data-language-id="{{ $offer->subject->subCategory->category->language->id }}"
                      
                        data-category-id="{{ $offer->subject->subCategory->category->id }}"
                        data-sub-category-id="{{ $offer->subject->subCategory->id }}"
                        data-subject-id="{{ $offer->subject->id }}"
                        data-discount="{{ $offer->discount }}"
                        data-valid-until="{{ $offer->valid_until }}"
                        data-mode="{{ $offer->mode }}"
                        data-status="{{ $offer->status }}"
                         data-banner="{{$offer->banner}}"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('offers.destroy', $offer->id) }}" method='POST'>
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

    @if(request()->data != 'all')
    <div class="flex justify-between items-center">
        <div style="width: 92%;">
            {{ $offers->appends(request()->query())->links() }}

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
                <input type="text" id="name" placeholder="Offer Name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="name" required />
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

            <!-- Discount & Valid Until -->
<div class="flex justify-between mb-3">
    <input type="text" id="discount" placeholder="Discount" name="discount" style="margin-right: 10px;"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
        required />

    <input type="text" id="valid_until" name="valid_until"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/2 p-2.5"
        required />
</div>

<!-- Mode Dropdown -->
<div class="mb-3">
    <select id="mode" name="mode"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        <option value="">Choose a Mode</option>
        <option value="Regular">Regular</option>
        <option value="Upgrade Plan">Upgrade Plan</option>
    </select>
</div>

<div class="mb-3">
  
    <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        <option value="">Select Status</option>
        <option value="1">Enabled</option>
        <option value="0">Disabled</option>
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
            document.getElementById('modalTitle').innerText = 'Create Offers';
            document.getElementById('modalForm').action = "{{ route('offers.store') }}";
            document.getElementById('modalForm').method = 'POST';
            document.getElementById('modalForm').querySelector('input[name="_method"]').value = '';
            document.getElementById('name').value = '';
            document.getElementById('select_language').value = '';
            document.getElementById('select_category').value = '';
            document.getElementById('select_sub_category').value = '';
            document.getElementById('select_subject').value = '';
            document.getElementById('offerImage').src = '/dummy.jpg';
            document.getElementById('modal').style.display = 'flex';
        });

        const editButtons = document.querySelectorAll('.editButton');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const languageId = this.getAttribute('data-language-id');
                // const photo = this.getAttribute('data-photo');
                const categoryId = this.getAttribute('data-category-id');
                const subCategoryId = this.getAttribute('data-sub-category-id');
                const subjectId = this.getAttribute('data-subject-id');
                const discount = this.getAttribute('data-discount');
                const validUntil = this.getAttribute('data-valid-until');
                const mode = this.getAttribute('data-mode');
                const status = this.getAttribute('data-status');
                const banner = this.getAttribute('data-banner');
 
                // Set modal for editing a subcategory
                document.getElementById('modalTitle').innerText = 'Edit Offers';
                document.getElementById('modalForm').action = `/offers/${id}`;
                document.getElementById('modalForm').method = 'POST';
                document.getElementById('modalForm').querySelector('input[name="_method"]').value = 'PUT';
                document.getElementById('name').value = name;
                document.getElementById('select_language').value = languageId;
                document.getElementById('select_category').value = categoryId;
                document.getElementById('select_sub_category').value = subCategoryId;
                document.getElementById('select_subject').value = subjectId;
                document.getElementById('discount').value = discount;
            document.getElementById('valid_until').value = validUntil;
            document.getElementById('mode').value = mode;
            document.getElementById('status').value = status;
            document.getElementById('offerImage').src = banner ? `/storage/${banner}` : '/dummy.jpg';

            // Handling status checkboxes
            // if (status === "1") {
            //     document.getElementById('status_enabled').checked = true;
            //     document.getElementById('status_disabled').checked = false;
            // } else {
            //     document.getElementById('status_enabled').checked = false;
            //     document.getElementById('status_disabled').checked = true;
            // }
                // document.getElementById('topicImage').src = photo ? `/storage/${photo}` : '/dummy.jpg';
                document.getElementById('modal').style.display = 'flex';
            });
        });

  


    });
</script>

@endpush