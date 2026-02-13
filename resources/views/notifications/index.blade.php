@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Notification</h1>
    <div class="flex justify-end items-center gap-2">
        <button id="createButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Create
        </button>

        <form action="{{ route('notifications.index') }}" method="GET" class="mb-0">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Notifications..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
        </form>
    </div>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">ID</th>
                <th scope="col" class="px-6 py-3">Title</th>
                <th scope="col" class="px-6 py-3">Message</th>
                <th scope="col" class="px-6 py-3">Image</th>
                <th scope="col" class="px-6 py-3">Type</th>
                <th scope="col" class="px-6 py-3">Schedule</th>
                <th scope="col" class="px-6 py-3">Date Sent</th>
                <th scope="col" class="px-6 py-3">Operate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notifications as $notification)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <td class="px-6 py-4">{{ $notification->id }}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $notification->title }}</td>
                <td class="px-6 py-4">{{ Str::limit($notification->message, 50) }}</td>
                <td class="px-6 py-4">
                     @if($notification->image)
                        <img src="{{ asset('storage/' . $notification->image) }}" class="w-12 h-12 object-cover rounded">
                     @else
                        no image
                     @endif
                </td>
                <td class="px-6 py-4">{{ $notification->type }}</td>
                <td class="px-6 py-4">{{ $notification->schedule_at ? $notification->schedule_at->format('Y-m-d H:i:s') : '-' }}</td>
                <td class="px-6 py-4">{{ $notification->sent_at ? $notification->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                <td class="px-6 py-4 flex gap-2">
                    <!-- <button class="editButton text-blue-600 hover:underline" data-id="{{ $notification->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button> -->
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center">No Notifications Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">
        {{ $notifications->links() }}
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 85%; max-height: 90vh; margin: auto; padding: 24px; position: relative; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Send Notification</h2>
            <button id="closeModal" style="background: none; border: 1px solid black; cursor: pointer; color: #6B7280; border-radius: 100%; width: 25px;">X</button>
        </div>

        <form id="notificationForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="POST">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Left Side: User Selection (5/12 width) -->
                <div id="userSelectionArea" class="md:col-span-5 border-r pr-4">
                    <div class="flex flex-col mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-semibold">Select Users</h3>
                        </div>
                        <input type="text" id="userSearch" placeholder="Search Users..." class="border rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="overflow-y-auto max-h-[400px] border rounded-lg shadow-sm">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 w-10"><input type="checkbox" id="selectAllUsers" class="rounded"></th>
                                    <th class="px-4 py-3">User Details</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                @foreach($users as $user)
                                <tr class="border-b user-row hover:bg-gray-50">
                                    <td class="px-4 py-3"><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded"></td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Side: Form Fields (7/12 width) -->
                <div class="md:col-span-7 space-y-6 mt-6 md:mt-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-900">Type</label>
                            <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="Announcement">Announcement</option>
                                <option value="Notification">Notification</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-900">Schedule at (Optional)</label>
                            <input type="datetime-local" id="schedule_at" name="schedule_at" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900">Title</label>
                        <input type="text" id="title" name="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500" placeholder="Notification Title" required>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900">Message</label>
                        <textarea id="message" name="message" rows="5" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Notification Message..." required></textarea>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-900 cursor-pointer">
                            <input type="checkbox" id="include_image" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 accent-blue-600">
                            Include Image Upload
                        </label>
                        <div id="image_input_container" class="hidden mt-3">
                            <input type="file" id="image_input" name="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none">
                            <p class="mt-1 text-xs text-gray-500">Allowed types: png, jpg, jpeg, gif (Max: 2MB)</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                         <button type="submit" id="submitBtn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-bold rounded-lg text-base px-10 py-3 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 shadow-lg transform transition active:scale-95">
                             Send Notification
                         </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@include('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modal');
        const form = document.getElementById('notificationForm');
        const includeImageCheckbox = document.getElementById('include_image');
        const imageInput = document.getElementById('image_input');
        const imageInputContainer = document.getElementById('image_input_container');
        const selectAllUsers = document.getElementById('selectAllUsers');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const userRows = document.querySelectorAll('.user-row');
        const userSearch = document.getElementById('userSearch');

        // Select All Users Logic
        selectAllUsers.addEventListener('change', function() {
            userCheckboxes.forEach(cb => {
                if (cb.closest('.user-row').style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
        });

        // User Search Logic
        userSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            userRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Toggle Image Input
        includeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                imageInputContainer.classList.remove('hidden');
            } else {
                imageInputContainer.classList.add('hidden');
                imageInput.value = '';
            }
        });

        // Handle Create Button
        document.getElementById('createButton').addEventListener('click', function() {
             document.getElementById('modalTitle').innerText = 'Send Notification';
             form.action = "{{ route('notifications.store') }}";
             form.querySelector("input[name='_method']").value = 'POST';
             form.reset();
             
             // Reset checkboxes
             selectAllUsers.checked = false;
             userCheckboxes.forEach(cb => cb.checked = false);
             userRows.forEach(row => row.style.display = '');
             userSearch.value = '';

             imageInputContainer.classList.add('hidden');
             includeImageCheckbox.checked = false;
             modal.style.display = 'flex';
        });

        // Handle Close Modal
        document.getElementById('closeModal').addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Edit Logic
        document.querySelectorAll('.editButton').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`/notifications/${id}/edit`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    const n = data.notification;
                    document.getElementById('modalTitle').innerText = 'Edit Notification';
                    form.action = `/notifications/${id}`;
                    form.querySelector("input[name='_method']").value = 'PUT';

                    document.getElementById('title').value = n.title;
                    document.getElementById('message').value = n.message;
                    document.getElementById('type').value = n.type;
                    
                    if(n.schedule_at) {
                        let date = new Date(n.schedule_at);
                        document.getElementById('schedule_at').value = date.toISOString().slice(0, 16);
                    } else {
                        document.getElementById('schedule_at').value = '';
                    }

                    // Reset checkboxes for edit (might need a way to load selected users if stored separately)
                    selectAllUsers.checked = false;
                    userCheckboxes.forEach(cb => cb.checked = false);

                    imageInputContainer.classList.add('hidden');
                    includeImageCheckbox.checked = false;

                    modal.style.display = 'flex';
                });
            });
        });

        // Form Submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = 'Sending...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.style.display = 'none';
                    location.reload();
                } else if (data.errors) {
                    let msg = '';
                    for (const [k, v] of Object.entries(data.errors)) msg += v.join('\n') + '\n';
                    alert(msg);
                }
            })
            .catch(err => alert('An error occurred.'))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            });
        });
    });
</script>
@endpush
