@extends('layouts.app')
@section('title', 'Users')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
    <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 dark:text-white">User List</h1>
    
    <div class="flex flex-wrap gap-4 items-center w-full md:w-auto justify-end">
        <!-- Search Form -->
        <form action="{{ route('users.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-64 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
            <button type="submit" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </form>

        <div class="flex gap-2">
            <!-- Import Button & Form -->
            <form id="importForm" action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" id="importFile" name="file" accept=".xlsx,.csv" onchange="document.getElementById('importForm').submit()">
            </form>
            <button type="button" onclick="document.getElementById('importFile').click()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center gap-2 text-sm font-semibold transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import
            </button>

            <!-- Export Button -->
            <a href="{{ route('users.export') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center gap-2 text-sm font-semibold transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export
            </a>
        </div>
    </div>
</div>

<div class="relative overflow-x-auto sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mb-4">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Id</th>
                <th scope="col" class="px-6 py-3">Name / Profile Photo</th>

                <th scope="col" class="px-6 py-3">Phone Number / Email Id</th>
                <th scope="col" class="px-6 py-3">Language / Category</th>
                <th scope="col" class="px-6 py-3">Coins</th>
                <th scope="col" class="px-6 py-3">Login Type</th>
                <th scope="col" class="px-6 py-3">Courses</th>
                <th scope="col" class="px-6 py-3">Register Date</th>

                <th scope="col" class="px-6 py-3">Plans</th>
                <th scope="col" class="px-6 py-3">Referral Code</th>
                <th scope="col" class="px-6 py-3">Friend Code</th>
                <th scope="col" class="px-6 py-3">Status</th>
                {{-- <th scope="col" class="px-6 py-3">Language</th>
                <th scope="col" class="px-6 py-3">Category</th> --}}
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

                <td class="px-6 py-4">
                    <span>{{ $user->category->language->name ?? '-' }} / {{ $user->category->name ?? '-' }}</span> 
                </td>

                <td class="px-6 py-4 font-semibold text-gray-600">
                    {{ $user->coins > 0 ? $user->coins : '-' }}
                </td>
              
                <td class="px-6 py-4">{{ $user->login_type }}</td>
                <td class="px-6 py-4 text-center">
                    <button class="courseInfoButton text-blue-600 hover:text-blue-800" data-id="{{ $user->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14m0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16" />
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 .935-.252 1.064-.598l.088-.416c.073-.34.134-.569.288-.569.165 0 .21.207.138.577l-.088.415c-.194.897-.728 1.319-1.532 1.319-1.2 0-1.785-.805-1.532-2.084l.738-3.468c.194-.897.728-1.319 1.532-1.319.545 0 .935.252 1.064.598l.088.416c.073.34.134.569.288.569.165 0 .21-.207.138-.577l-.088-.415c-.194-.897-.728-1.319-1.532-1.319zm-.93-2.588a.905.905 0 1 1 0 1.81.905.905 0 0 1 0-1.81" />
                        </svg>
                    </button>
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

                {{-- <td class="px-6 py-4">{{ $user->category->language->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $user->category->name ?? 'N/A' }}</td> --}}
                <td class="px-6 py-4 flex gap-4">
                    <button class="editButton font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        data-id="{{ $user->id }}"
                        data-status="{{ $user->status }}">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('users.delete', $user->id) }}" method='POST'>
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
                <td colspan="14" align="center">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
</div>

    </div>
</div>

<!-- Edit Modal -->
<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 30%; max-height: 90vh; margin: auto; padding: 24px; position: relative; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Modal Title</h2>
            <button id="closeModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>
        <form id="modalForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Add Coins:</label>
                <input type="number" name="coins" id="coins" class="border rounded px-2 py-1 w-full" placeholder="Enter coins">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                <select name="status" id="status" class="border rounded px-2 py-1 w-full">
                    <option value="enabled">Enabled</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>
            <button type="submit" style="background-color: #2563EB; color: white; font-size: 14px; font-weight: 500; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;">Update</button>
        </form>
    </div>
</div>

<!-- Course Info Modal -->
<div id="courseModal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 60; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white; border-radius: 10px; width: 50%; max-height: 90vh; margin: auto; padding: 24px; position: relative; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="courseModalTitle" style="font-size: 1.5rem; font-weight: bold;">User Courses</h2>
            <button id="closeCourseModal" style="background: none;border: 1px solid black;cursor: pointer;color: #6B7280;border-radius: 100%;width: 25px;">X</button>
        </div>
        <table class="w-full text-sm text-left text-gray-500 border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Course Name</th>
                    <th class="px-4 py-2 border">Start Date</th>
                    <th class="px-4 py-2 border">End Date</th>
                    <th class="px-4 py-2 border">Status</th>
                </tr>
            </thead>
            <tbody id="courseTableBody">
                <tr>
                    <td colspan="4" class="text-center py-3">Loading...</td>
                </tr>
            </tbody>
        </table>
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

        // Course Info Modal Logic
        const courseModal = document.getElementById('courseModal');
        const closeCourseModal = document.getElementById('closeCourseModal');
        const courseTableBody = document.getElementById('courseTableBody');

        document.querySelectorAll('.courseInfoButton').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                courseModal.style.display = 'flex';
                courseTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-3">Loading...</td></tr>';

                fetch(`/users/${userId}/courses`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.courses.length > 0) {
                            courseTableBody.innerHTML = data.courses.map(course => `
                                <tr class="border-b">
                                    <td class="px-4 py-2 border">${course.name}</td>
                                    <td class="px-4 py-2 border">${course.start_date}</td>
                                    <td class="px-4 py-2 border">${course.end_date}</td>
                                    <td class="px-4 py-2 border">${course.status}</td>
                                </tr>
                            `).join('');
                        } else {
                            courseTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-3">No active courses found.</td></tr>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        courseTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-red-500">Error loading data.</td></tr>';
                    });
            });
        });

        closeCourseModal.addEventListener('click', () => courseModal.style.display = 'none');
        closeModal.addEventListener('click', () => modal.style.display = 'none');
    });
</script>

@endpush