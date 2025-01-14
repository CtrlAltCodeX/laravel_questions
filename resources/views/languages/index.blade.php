@extends('layouts.app')

@section('title', 'Language')

@section('content')
<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Languages</h1>
    <button id="createButton" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
        Create
    </button>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Name</th>
                <th scope="col" class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $language)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <td class="px-6 py-4">{{ $language->id }}</td>
                <td class="px-6 py-4">{{ $language->name }}</td>
                <td class="px-6 py-4 flex gap-4">
                    <button
                        class="editButton font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        data-id="{{ $language->id }}"
                        data-name="{{ $language->name }}">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                        </svg>
                    </button>

                    <form action="{{ route('languages.destroy', $language->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modal" style="display: none; position: fixed; inset: 0; align-items: center; justify-content: center; z-index: 50; background-color: rgba(0, 0, 0, 0.5);">
    <div style="background-color: white;border-radius: 10px;box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);width: 30%;margin: auto;padding: 24px;position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 id="modalTitle" style="font-size: 1.5rem; font-weight: bold;">Modal Title</h2>
            <button id="closeModal" style="background: none; border: none; cursor: pointer; color: #6B7280;">
                X
            </button>
        </div>
        <form id="modalForm" method="POST" action="">
            @csrf
            <input type="hidden" name="_method" value="">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #374151;">Name</label>
                <input type="text" id="name" name="name" required
                    style="width: 100%; padding: 8px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; color: #111827; background-color: #F9FAFB;">
            </div>
            <button type="submit" style="background-color: #2563EB; color: white; font-size: 14px; font-weight: 500; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;">Save</button>
        </form>
    </div>
</div>




@endsection


@push('scripts')

@include('script')


<script>
    $(document).ready(function() {

        document.getElementById('createButton').addEventListener('click', function() {
            document.getElementById('modalTitle').innerText = 'Create Language';
            document.getElementById('modalForm').action = "{{ route('languages.store') }}";
            document.getElementById('modalForm').querySelector('input[name="_method"]').value = '';
            document.getElementById('name').value = '';
            document.getElementById('modal').style.display = 'flex';
        });

        document.querySelectorAll('.editButton').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalTitle').innerText = 'Edit Language';
                document.getElementById('modalForm').action = "{{ route('languages.update', '') }}/" + this.dataset.id;
                document.getElementById('modalForm').querySelector('input[name="_method"]').value = 'PUT';
                document.getElementById('name').value = this.dataset.name;
                document.getElementById('modal').style.display = 'flex';
            });
        });



        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) {
                document.getElementById('modal').style.display = 'none';
            }
        });
    });
</script>

@endpush