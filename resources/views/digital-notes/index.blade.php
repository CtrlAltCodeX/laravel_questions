@extends('layouts.app')

@section('title', 'Digital Notes')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Digital Notes</h1>
    <div class="flex justify-end items-center gap-2">
        <button id="createButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Create
        </button>

        <form action="{{ route('digital-notes.index') }}" method="GET" class="mb-0">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Notes..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
        </form>
    </div>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="notesTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Photo</th>
                <th scope="col" class="px-6 py-3">Name</th>
                <th scope="col" class="px-6 py-3">Language</th>
                <th scope="col" class="px-6 py-3">Main Category</th>
                <th scope="col" class="px-6 py-3">Sub Category</th>
                <th scope="col" class="px-6 py-3">Subject</th>
                <th scope="col" class="px-6 py-3">Topic</th>
                <th scope="col" class="px-6 py-3">HTML Code</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($digitalNotes as $note)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <td class="px-6 py-4">{{ $note->id }}</td>
                <td class="px-6 py-4">
                     <img src="{{ $note->photo ? '/storage/'.$note->photo : '/dummy.jpg'}}" style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;' />
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $note->name }}</td>
                <td class="px-6 py-4">{{ $note->language->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $note->category->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $note->subCategory->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $note->subject->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $note->topic->name ?? '-' }}</td>
                <td class="px-6 py-4">
                    <button class="previewButton text-blue-600 hover:text-blue-900" data-id="{{ $note->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    </button>
                </td>
                <td class="px-6 py-4 {{ $note->status ? 'text-green-600' : 'text-red-600' }}">
                    {{ $note->status ? 'Enable' : 'Disable' }}
                </td>
                <td class="px-6 py-4 flex gap-4">
                    <button class="editButton font-medium text-blue-600 dark:text-blue-500 hover:underline" data-id="{{ $note->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('digital-notes.destroy', $note->id) }}" method='POST' onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="font-medium text-danger dark:text-danger-500 hover:underline">
                             <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="px-6 py-4 text-center">No Digital Notes Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">
        {{ $digitalNotes->appends(request()->query())->links() }}
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 60%; max-height: 90vh; margin: auto; padding: 24px; position: relative; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Create Digital Notes</h2>
            <button id="closeModal" style="background: none; border: 1px solid black; cursor: pointer; color: #6B7280; border-radius: 100%; width: 25px;">X</button>
        </div>

        <form id="digitalNoteForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="POST">
            
            <div class="mb-4">
                 <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Photo</label>
                 <input type="file" name="photo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Note Name" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Language</label>
                    <select id="language_id" name="language_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                        <option value="">Select Language</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}">{{ $language->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
                    <select id="category_id" name="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                        <option value="">Select Category</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sub Category</label>
                    <select id="sub_category_id" name="sub_category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Select Sub Category</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject</label>
                    <select id="subject_id" name="subject_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Select Subject</option>
                    </select>
                </div>

                <div class="mb-4 col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Topic</label>
                    <select id="topic_id" name="topic_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">Select Topic</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">HTML Code</label>
                <textarea id="content" name="content" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter HTML Code here..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

            <button type="submit" id="submitBtn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Save</button>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 60; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 80%; height: 90vh; margin: auto; padding: 10px; position: relative; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 0 14px;">
            <h2 style="font-size: 1.5rem; font-weight: bold;">HTML Preview</h2>
            <button id="closePreviewModal" style="background: none; border: 1px solid black; cursor: pointer; color: #6B7280; border-radius: 100%; width: 25px;">X</button>
        </div>
        <iframe id="previewFrame" style="flex-grow: 1; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></iframe>
    </div>
</div>

@section('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 300px;
    }
</style>
@endsection

@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>
@include('script')
<script>
    let noteEditor;
    document.addEventListener('DOMContentLoaded', function() {
        
        ClassicEditor
            .create(document.querySelector('#content'))
            .then(editor => {
                noteEditor = editor;
            })
            .catch(error => {
                console.error(error);
            });

        // Handle Create Button
        document.getElementById('createButton').addEventListener('click', function() {
             document.getElementById('modalTitle').innerText = 'Create Digital Notes';
             const form = document.getElementById('digitalNoteForm');
             form.action = "{{ route('digital-notes.store') }}";
             form.querySelector("input[name='_method']").value = 'POST';
             
             // Reset fields
             form.reset();
             if (noteEditor) noteEditor.setData('');
             document.getElementById('content').value = '';
             
             // Clear dropdowns except language
             clearDropdown('category_id');
             clearDropdown('sub_category_id');
             clearDropdown('subject_id');
             clearDropdown('topic_id');
 
             document.getElementById('modal').style.display = 'flex';
        });

        // Handle Close Modal
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('modal').style.display = 'none';
        });

        // Handle Form Submit
        // NOTE: We use ID 'digitalNoteForm' to avoid conflict with global 'modalForm' listener in script.blade.php
        document.getElementById('digitalNoteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            if (noteEditor) {
                document.getElementById('content').value = noteEditor.getData();
            }
            const formData = new FormData(form);
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = 'Saving...';
            
            fetch(form.action, {
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
                    document.getElementById('modal').style.display = 'none';
                    location.reload(); 
                } else if (data.errors) {
                     // Validation errors
                    let errorMsg = '';
                    for (const [key, value] of Object.entries(data.errors)) {
                        errorMsg += value.join('\n') + '\n';
                    }
                    alert('Error:\n' + errorMsg);
                } else {
                    // Unknown error
                    alert('An unknown error occurred.');
                    console.error('Unknown response:', data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            });
        });

        // Dropdown Logic
        document.getElementById('language_id').addEventListener('change', function() {
            fetchOptions('/get-categories/' + this.value, 'category_id');
        });

        document.getElementById('category_id').addEventListener('change', function() {
            fetchOptions('/get-subcategories/' + this.value, 'sub_category_id');
        });

        document.getElementById('sub_category_id').addEventListener('change', function() {
            fetchOptions('/get-subjects/' + this.value, 'subject_id');
        });

        document.getElementById('subject_id').addEventListener('change', function() {
            fetchOptions('/get-topics/' + this.value, 'topic_id');
        });

        function fetchOptions(url, targetId, selectedValue = null) {
            const target = document.getElementById(targetId);
            clearDropdown(targetId);
            // Clear subsequent dropdowns
             if(targetId === 'category_id') {
                clearDropdown('sub_category_id');
                clearDropdown('subject_id');
                clearDropdown('topic_id');
             } else if(targetId === 'sub_category_id') {
                 clearDropdown('subject_id');
                 clearDropdown('topic_id');
             } else if(targetId === 'subject_id') {
                 clearDropdown('topic_id');
             }

            if (!url.split('/').pop()) return; // Don't fetch if no ID

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        if(selectedValue && item.id == selectedValue) option.selected = true;
                        target.appendChild(option);
                    });
                })
                .catch(err => console.error(err));
        }

        function clearDropdown(id) {
            const select = document.getElementById(id);
            select.innerHTML = '<option value="">Select ' + id.replace('_id', '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';
        }


        // Edit Logic
        document.querySelectorAll('.editButton').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Fetch note data
                fetch(`/digital-notes/${id}/edit`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    const note = data.note;
                    const lists = data.dropdown_list;
                    
                    document.getElementById('modalTitle').innerText = 'Edit Digital Notes';
                    const form = document.getElementById('digitalNoteForm');
                    form.action = `/digital-notes/${id}`;
                    form.querySelector("input[name='_method']").value = 'PUT';

                    document.getElementById('name').value = note.name;
                    document.getElementById('status').value = note.status ? 1 : 0;
                    if (noteEditor) {
                        noteEditor.setData(note.content || '');
                    } else {
                        document.getElementById('content').value = note.content || '';
                    }

                    document.getElementById('language_id').value = note.language_id;
                    
                    // Populate lists
                    populateDropdown('category_id', lists.categories, note.category_id);
                    populateDropdown('sub_category_id', lists.subCategories, note.sub_category_id);
                    populateDropdown('subject_id', lists.subjects, note.subject_id);
                    populateDropdown('topic_id', lists.topics, note.topic_id);

                    document.getElementById('modal').style.display = 'flex';
                });
            });
        });

        function populateDropdown(id, list, selectedValue) {
             const select = document.getElementById(id);
             clearDropdown(id);
             list.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.name;
                if(item.id == selectedValue) option.selected = true;
                select.appendChild(option);
            });
        }


        // Preview Logic
        document.querySelectorAll('.previewButton').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                // Fetch content
                fetch(`/digital-notes/${id}`)
                    .then(res => res.json())
                    .then(data => {
                         let content = data.content || '';
                         
                         // Decode HTML entities if they exist (e.g. &lt; -> <)
                         const txt = document.createElement("textarea");
                         txt.innerHTML = content;
                         content = txt.value;

                         const iframe = document.getElementById('previewFrame');
                         const doc = iframe.contentWindow.document;
                         doc.open();
                         doc.write(content);
                         doc.close();
                         document.getElementById('previewModal').style.display = 'flex';
                    })
                    .catch(err => alert('Failed to load preview'));
            });
        });

        document.getElementById('closePreviewModal').addEventListener('click', function() {
            document.getElementById('previewModal').style.display = 'none';
        });
    });
</script>
@endpush
