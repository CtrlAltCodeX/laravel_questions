@extends('layouts.app')
@section('title', 'Users')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">User List</h1>
</div>

<div class="relative overflow-x-auto sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mb-4">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Id</th>
                <th scope="col" class="px-6 py-3">Name / Profile Photo</th>

                <th scope="col" class="px-6 py-3">Phone Number / Email Id</th>
                <th scope="col" class="px-6 py-3">Coins</th>
                <th scope="col" class="px-6 py-3">Login Type</th>
                <th scope="col" class="px-6 py-3"> Courses</th>
                <th scope="col" class="px-6 py-3">Start Dates</th>

                <th scope="col" class="px-6 py-3">Plans</th>
                <th scope="col" class="px-6 py-3">Referral Code</th>
                <th scope="col" class="px-6 py-3">Friend Code</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Language</th>
                <th scope="col" class="px-6 py-3">Category</th>
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $user->id }}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="flex flex-col items-center">
                        <span>{{ $user->name }}</span>
                        @if($user->profile_photo)
                        <img src="{{ asset('storage/'.$user->profile_photo) }}" alt="Profile Photo" class="w-10 h-10 rounded-full mt-1">
                        @else
                        <span class="text-gray-400 mt-1">No Image</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-600">{{ $user->phone_number ?? '-' }}</span>
                        <span>{{ $user->email ?? '-' }}</span>
                    </div>
                </td>

                <td class="px-6 py-4 font-semibold text-gray-600">
                    {{ $user->total_coins > 0 ? $user->total_coins : '-' }}
                </td>
              
                <td class="px-6 py-4">{{ $user->login_type }}</td>
                {{-- Course Names --}}
                <td class="px-6 py-4 text-center">
                    {!! $user->userCourses->pluck('course.name')->filter()->implode(', ') ?: '<span class="font-semibold text-gray-600">-</span>' !!}
                </td>

                {{-- Start Dates --}}
                <td class="px-6 py-4 font-semibold text-gray-600">
                    {{ $user->userCourses->pluck('created_at')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d-m-Y'))->implode(', ')?:'-' }}
                </td>

                <td class="px-6 py-4 font-semibold text-gray-600">{{ $user->plans ?? '-' }}</td>
                <td class="px-6 py-4 font-semibold text-gray-600">{{ $user->referral_code ?? '-' }}</td>
                <td class="px-6 py-4 font-semibold text-gray-600">{{ $user->friend_code ?? '-' }}</td>
                <td class="px-6 py-4">
                    @if($user->status == "Enabled")
                    <span class="text-green-500 font-bold uppercase">Enabled</span>
                    @else
                    <span class="text-red-500 font-bold uppercase">Disabled</span>
                    @endif
                </td>

                <td class="px-6 py-4">{{ $user->category->language->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $user->category->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">
                    <button class="editButton font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        data-id="{{ $user->id }}"
                        data-status="{{ $user->status }}">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" align="center">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
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

            <div class="mb-3 flex items-center space-x-4">
                <label class="font-semibold" style="margin-right: 0px;">Add Coins:</label>
                <input type="number" name="coins" id="coins" class="border rounded px-2 py-1 w-full" placeholder="Enter coins">
            </div>


            <div class="mb-3 flex items-center space-x-4">
                <label class="font-semibold" style="margin-right: 10px;">Status:</label>
                <select name="status" id="status" class="border rounded px-2 py-1 w-full">
                    <option value="enabled">Enabled</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Data</label>
                <textarea id="meta_data" name="meta_data"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                   focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                   dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                   dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Enter meta description here..."></textarea>
            </div>
            <button type="submit" style="background-color: #2563EB; color: white; font-size: 14px; font-weight: 500; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;">
                Update
            </button>
        </form>
    </div>
</div>


@endsection

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
            var output = document.getElementById('offerImage');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const editButtons = document.querySelectorAll('.editButton');
        const modal = document.getElementById('modal');
        const closeModal = document.getElementById('closeModal');
        const statusDropdown = document.getElementById('status');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const status = this.getAttribute('data-status');

                document.getElementById('modalTitle').innerText = 'User Edit';
                const modalForm = document.getElementById('modalForm');
                modalForm.action = `/users/update-coins-status/${id}`;
                modalForm.method = 'POST';
                modalForm.querySelector('input[name="_method"]').value = 'PUT';


                if (status.toLowerCase() === "enabled") {
                    statusDropdown.value = "enabled";
                } else {
                    statusDropdown.value = "disabled";
                }

                modal.style.display = 'flex';
            });
        });





    });
</script>

@endpush