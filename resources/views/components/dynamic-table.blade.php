<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="offersTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                @foreach($columns as $key => $column)
                @php
                    $sortDir = array_merge(request()->all(), ['sort' => $key, 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']);
                @endphp
                <th scope="col" class="px-6 py-3">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => $sortDir['sort'], 'direction' => $sortDir['direction']]) }}">
                        {{ $column }}
                        @if (request()->get('sort') == $key)
                        @if (request()->get('direction', 'desc') == 'asc')
                        ▲
                        @else
                        ▼
                        @endif
                        @endif
                    </a>
                </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($rowData as $data)
            <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                @forelse($rows as $key => $row)
                    @php $value = $row['value']; @endphp
                    @switch($row['type'])
                        @case ('image')
                            <td class="px-4 py-3 text-center">
                                <img src="{{ $data->banner ? '/uploads/courses/'.$data->banner : '/dummy.jpg' }}" style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border:2px solid black;' />
                            </td>
                            @break;
                        @case ('action')
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-3">
                                    <button class="text-blue-600 hover:underline editButton" data='@json($data)'>
                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30">
                                            <path d="M 22.828125 3 C 22.316375 3 21.804562 3.1954375 21.414062 3.5859375 L 19 6 L 24 11 L 26.414062 8.5859375 C 27.195062 7.8049375 27.195062 6.5388125 26.414062 5.7578125 L 24.242188 3.5859375 C 23.851688 3.1954375 23.339875 3 22.828125 3 z M 17 8 L 5.2597656 19.740234 C 5.2597656 19.740234 6.1775313 19.658 6.5195312 20 C 6.8615312 20.342 6.58 22.58 7 23 C 7.42 23.42 9.6438906 23.124359 9.9628906 23.443359 C 10.281891 23.762359 10.259766 24.740234 10.259766 24.740234 L 22 13 L 17 8 z M 4 23 L 3.0566406 25.671875 A 1 1 0 0 0 3 26 A 1 1 0 0 0 4 27 A 1 1 0 0 0 4.328125 26.943359 A 1 1 0 0 0 4.3378906 26.939453 L 4.3632812 26.931641 A 1 1 0 0 0 4.3691406 26.927734 L 7 26 L 5.5 24.5 L 4 23 z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route($value, $data->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
                            @break;
                        @case ('text')
                            <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">{{ $data->$value }}</td>
                            @break;
                    @endswitch
                @endforeach
            </tr>
            @empty
            <tr>
                <td class="px-4 py-4 text-center text-gray-500">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(request()->data != 'all')
    <div class="flex justify-between items-center">
        <div style="width: 92%;">
            {{ $rowData->appends(request()->query())->links() }}
        </div>

        <div>
            <a href="{{ request()->fullUrlWithQuery(['data' => 'all']) }}"
                class="bg-blue-500 text-white rounded hover:bg-blue-600 p-2">
                View All
            </a>
        </div>
    </div>
    @endif
</div>