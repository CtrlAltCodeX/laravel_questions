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
                    Course IDs
                </th>
                <th scope="col" class="px-6 py-3">Subscription</th>
<th scope="col" class="px-6 py-3">Discount</th>
<th scope="col" class="px-6 py-3">Upgrade</th>

            
                  <th scope="col" class="px-6 py-3">
                    Valid Till
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
                    <img src="{{ $offer->banner ? '/uploads/offers/'.$offer->banner : '/dummy.jpg'}}" style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;' />
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                      {{$offer->name}}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
               {{ implode(', ', json_decode($offer->course, true) ?? []) }}


                </th>
            
             
     <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
    @foreach($offer->subscription_prepared as $sub)
        <div>{{ $sub['type'] }}</div>
    @endforeach
</th>

<th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
    @foreach($offer->subscription_prepared as $sub)
        <div>{{ $sub['discount'] }}</div>
    @endforeach
</th>

<th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
    @foreach($offer->subscription_prepared as $sub)
        <div>{{ $sub['upgrade'] }}</div>
    @endforeach
</th>

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ \Carbon\Carbon::parse($offer->valid_from)->format('d M Y') }} to 
{{ \Carbon\Carbon::parse($offer->valid_to)->format('d M Y') }}

                </th>
             
           

                <th class="px-6 py-4 font-medium 
    {{ $offer->status == 1 ? 'text-blue-600' : 'text-red-600' }}">
    {{ $offer->status == 1 ? 'Enabled' : 'Disabled' }}
</th>


                <td class="px-6 py-4 flex gap-4">
                <button
  class="editButton font-medium text-blue-600 dark:text-blue-500 hover:underline"
  data-id="{{ $offer->id }}"
  data-name="{{ $offer->name }}"
  data-valid_from="{{ $offer->valid_from }}"
  data-valid_to="{{ $offer->valid_to }}"
  data-status="{{ $offer->status }}"
  data-banner="{{ $offer->banner }}"
  data-courses='@json($offer->course)'
data-subscriptions='@json(json_decode($offer->subscription))'
>
  <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>     </button>
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

    <!-- Banner Upload -->
    <div class="mb-3 relative" style="height: 100px;">
        <div class="container">
            <input accept="image/*" type="file" class="opacity-0 w-[100] h-[100] absolute z-10 cursor-pointer" name="banner" style="width: 100px; height:100px;" id='fileInput' />
            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white image" src="/dummy.jpg" alt="" id='offerImage' style='width:100px;height:100px;'>
            <div class="bg-black/[0.5] overlay absolute h-[100%] top-[0px] w-[100px] rounded-full opacity-0 flex justify-center items-center text-white">Upload Pic</div>
        </div>
    </div>

    <!-- Name -->
    <div class="mb-3">
        <input type="text" id="name" placeholder="Offer Name" name="name"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" />
        <div style="color: red;" id="error-name"></div>
    </div>


<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Course</label>
    <div class="mb-3 relative">
        <button type="button" onclick="toggleDropdown()" class=" w-full border border-gray-300 rounded-md px-4 py-2 text-left bg-white">
            <span id="CourseButtonLabel">Select Course</span>
        </button>
        <div id="dropdownMenu" style="max-height: 200px;" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-auto">
            <label class="flex items-center px-4 py-2">
                <input type="checkbox" onchange="toggleSelectAll(this)" />
                <span class="ml-2">Select All</span>
            </label>
            @foreach($Courses as $cur)
                <label class="flex items-center px-4 py-2">
                    <input type="checkbox" name="course[]" value="{{ $cur->id }}" class="Course-checkbox" data-name="{{ $cur->name }}" />
                    <span class="ml-2">{{ $cur->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>



<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Subscription Plans</label>

    <!-- Monthly -->
    <div class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="monthlyCheck" name="subscription[monthly][active]" value="1" class="subscriptionCheck" />
        <label for="monthlyCheck" class="w-24">Monthly</label>
        <input type="number" name="subscription[monthly][discount]" placeholder="Discount" class="border border-gray-300 rounded p-1 w-28" />
        
    </div>

    <!-- Semi Annual -->
    <div class="flex items-center gap-2 mb-2">
        <input type="checkbox" id="semiAnnualCheck" name="subscription[semi_annual][active]" value="1" class="subscriptionCheck" />
        <label for="semiAnnualCheck" class="w-24">Semi Annual</label>
        <input type="number" name="subscription[semi_annual][discount]" placeholder="Discount" class="border border-gray-300 rounded p-1 w-28" />
        <input type="number" name="subscription[semi_annual][upgrade]"  value=""  placeholder="upgrade " class="border border-gray-300 rounded p-1 w-36" />
    </div>

    <!-- Annual -->
    <div class="flex items-center gap-2">
        <input type="checkbox" id="annualCheck" name="subscription[annual][active]" value="1" class="subscriptionCheck" />
        <label for="annualCheck" class="w-24">Annual</label>
        <input type="number" name="subscription[annual][discount]" placeholder="Discount" class="border border-gray-300 rounded p-1 w-28" />
        <input type="number" name="subscription[annual][upgrade]" value="" max="" placeholder="upgrade " class="border border-gray-300 rounded p-1 w-36" />
    </div>

        <div style="color: red;" id="error-subscription"></div>

</div>


    <!-- Discount -->
    <!-- <div class="mb-3">
        <input type="text" id="discount" placeholder="Discount" name="discount"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" />
        <div id="error-discount" style="color: red;"></div>
    </div> -->

    <!-- Upgrade -->
    <!-- <div class="mb-3">
        <input type="text" id="upgrade" placeholder="Upgrade" name="upgrade"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" />
        <div id="error-upgrade" style="color: red;"></div>
    </div> -->


    <!-- Valid From -->
    <div class="mb-3">
        <label class="block mb-1">Valid From</label>
        <input type="date" id="valid_from" name="valid_from"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" />
        <div id="error-valid_from" style="color: red;"></div>
    </div>

    <!-- Valid To -->
    <div class="mb-3">
        <label class="block mb-1">Valid To</label>
        <input type="date" id="valid_to" name="valid_to"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" />
        <div id="error-valid_to" style="color: red;"></div>
    </div>

    <!-- Status -->
    <div class="mb-3">
        <select id="status" name="status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            <option value="">Select Status</option>
            <option value="1">Enabled</option>
            <option value="0">Disabled</option>
        </select>
        <div id="error-status" style="color: red;"></div>
    </div>

    <!-- Submit -->
    <button type="submit"
        style="background-color: #2563EB; color: white; font-size: 14px; font-weight: 500; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;">
        Save
    </button>
</form>

    </div>
</div>

@endsection

@push('scripts')

@include('script')

    <script>


  function toggleSelectAll(checkbox) {
    const allCourseCheckboxes = document.querySelectorAll('.Course-checkbox');
    allCourseCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateCourseButtonLabel();
}

function updateCourseButtonLabel() {
    const checkedCourses = Array.from(document.querySelectorAll('.Course-checkbox:checked'))
        .map(cb => cb.getAttribute('data-name'));
    
    document.getElementById('CourseButtonLabel').innerText = checkedCourses.length
        ? checkedCourses.join(', ')
        : 'Select Course';
}

// Attach event listener to individual course checkboxes
document.addEventListener('DOMContentLoaded', function () {
    const courseCheckboxes = document.querySelectorAll('.Course-checkbox');
    courseCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCourseButtonLabel);
    });
});


   function toggleDropdown() {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.classList.toggle('hidden');
    }
      // Close dropdown on outside click
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        const button = event.target.closest('button');
        if (!dropdown.contains(event.target) && !button) {
            dropdown.classList.add('hidden');
        }
    });

        document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('subscriptionDropdown');
        const button = event.target.closest('button');
        if (!dropdown.contains(event.target) && !button) {
            dropdown.classList.add('hidden');
        }
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

          document.getElementById('createButton').addEventListener('click', function () {
    // Set modal title and form action
    document.getElementById('modalTitle').innerText = 'Create Offers';
    document.getElementById('modalForm').action = "{{ route('offers.store') }}";
    document.getElementById('modalForm').method = 'POST';
    document.getElementById('modalForm').querySelector('input[name="_method"]').value = '';

    // Reset all input fields
    document.getElementById('name').value = '';
    // document.getElementById('discount').value = '';
    // document.getElementById('upgrade').value = '';
    document.getElementById('valid_from').value = '';
    document.getElementById('valid_to').value = '';
    document.getElementById('status').value = '';

    // Reset course checkboxes
    document.querySelectorAll('.Course-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.subscriptionCheck').forEach(cb => cb.checked = false);

    document.querySelector('input[type="checkbox"][onchange="toggleSelectAll(this)"]').checked = false;
    document.getElementById('CourseButtonLabel').innerText = 'Select Course';

  
    // Reset image
    document.getElementById('offerImage').src = '/dummy.jpg';
    document.getElementById('fileInput').value = '';

    // Clear error messages
    ['error-name', 'error-subscription', 'error-valid_from', 'error-valid_to', 'error-status']
        .forEach(id => document.getElementById(id).innerText = '');

    // Show modal
    document.getElementById('modal').style.display = 'flex';
});

    const editButtons = document.querySelectorAll('.editButton');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
         
            const validFrom = this.getAttribute('data-valid_from');
            const validTo = this.getAttribute('data-valid_to');
            const status = this.getAttribute('data-status');
            const banner = this.getAttribute('data-banner');
                const subscriptions = JSON.parse(this.getAttribute('data-subscriptions') || '{}');

             let courses = [];
        try {
            courses = JSON.parse(this.getAttribute('data-courses') || '[]');
        } catch (e) {
            courses = [];
        }
            // Set courses (checkboxes inside dropdown)
const courseCheckboxes = document.querySelectorAll('.Course-checkbox');
courseCheckboxes.forEach(checkbox => {
    const val = checkbox.value;
    checkbox.checked = courses.includes(val) || courses.includes(parseInt(val));
});

// OPTIONAL: Update dropdown label to show selected course names
const checkedCourses = Array.from(courseCheckboxes)
    .filter(cb => cb.checked)
    .map(cb => cb.getAttribute('data-name'));

document.getElementById('CourseButtonLabel').innerText = checkedCourses.length
    ? checkedCourses.join(', ')
    : 'Select Course';


     
              // Monthly
document.getElementById('monthlyCheck').checked = !!subscriptions.monthly;
document.querySelector('input[name="subscription[monthly][discount]"]').value = subscriptions.monthly?.discount || '';

// Semi Annual
document.getElementById('semiAnnualCheck').checked = !!subscriptions.semi_annual;
document.querySelector('input[name="subscription[semi_annual][discount]"]').value = subscriptions.semi_annual?.discount || '';
document.querySelector('input[name="subscription[semi_annual][upgrade]"]').value = subscriptions.semi_annual?.upgrade || '';

// Annual
document.getElementById('annualCheck').checked = !!subscriptions.annual;
document.querySelector('input[name="subscription[annual][discount]"]').value = subscriptions.annual?.discount || '';
document.querySelector('input[name="subscription[annual][upgrade]"]').value = subscriptions.annual?.upgrade || '';


            // Update modal fields
            document.getElementById('modalTitle').innerText = 'Edit Offers';
            const modalForm = document.getElementById('modalForm');
            modalForm.action = `/offers/${id}`;
            modalForm.method = 'POST';
            modalForm.querySelector('input[name="_method"]').value = 'PUT';

            document.getElementById('name').value = name;
            document.getElementById('valid_from').value = validFrom;
            document.getElementById('valid_to').value = validTo;
            document.getElementById('status').value = status;
            document.getElementById('offerImage').src = banner ? `/uploads/offers/${banner}` : '/dummy.jpg';

                    
            // Set checkboxes based on saved values
            const subCheckboxes = document.querySelectorAll('.subscription-checkbox');
            subCheckboxes.forEach(cb => {
                cb.checked = subscriptions.includes(cb.value);
            });

            // Update label
            const checkedSubs = Array.from(subCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.getAttribute('data-name'));

     
                        document.getElementById('modal').style.display = 'flex';
                    });
                });



        });
    </script>


<script>
function toggleSubscriptionDropdown() {
    document.getElementById('subscriptionDropdown').classList.toggle('hidden');
}

function toggleSelectAllSubscription(source) {
    const allSubs = document.querySelectorAll('.subscription-checkbox');
    allSubs.forEach(cb => cb.checked = source.checked);
    updateSubscriptionLabel();
}

function updateSubscriptionLabel() {
    const checked = Array.from(document.querySelectorAll('.subscription-checkbox:checked'))
        .map(cb => cb.getAttribute('data-name'));

    document.getElementById('subscriptionButtonLabel').innerText = checked.length
        ? checked.join(', ')
        : 'Select Subscription';
}

// Update label when individual checkbox clicked
document.addEventListener('DOMContentLoaded', function () {
    const subs = document.querySelectorAll('.subscription-checkbox');
    subs.forEach(cb => {
        cb.addEventListener('change', updateSubscriptionLabel);
    });
});

document.getElementById('createButton').addEventListener('click', function() {
    // Existing reset fields...

    // ✅ Default check all subscription checkboxes
    const subCheckboxes = document.querySelectorAll('.subscription-checkbox');
    subCheckboxes.forEach(cb => cb.checked = true);

    // ✅ Also check 'Select All'
    document.getElementById('selectAllSubscription').checked = true;

    // ✅ Update label text
    updateSubscriptionLabel();
});

</script>


@endpush
