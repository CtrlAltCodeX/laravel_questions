<aside id="logo-sidebar" class="min-h-screen w-1/5 bg-gray-100 dark:bg-gray-900 w-64 h-screen pt-5 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <a href="https://flowbite.com" class="flex ms-2 md:me-24 shadow p-2 w">
            <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Flowbite</span>
        </a>
        <ul class="space-y-2 font-medium">
            @foreach ($menuItems as $item)
            @if(!isset($item['sub-menus']))
            <li>
                <a href="{{ route($item['route']) }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                        <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                    </svg>
                    <span class="ms-3">{{$item['name']}}</span>
                </a>
            </li>
            @else
            <div class="mt-2">
                <button class="flex justify-between items-center w-full px-4 py-2 rounded ">
                    <span>{{$item['name']}}</span>
                    <svg class="w-4 h-4 transition-transform transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <!-- Dropdown Content -->
                <div class="mt-2 space-y-2 pl-6">
                    @foreach($item['sub-menus'] as $subMenu)
                    <a href="{{ route($subMenu['route']) }}" class="block px-4 py-2 rounded">{{$subMenu['name']}}</a>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
            <!-- Dropdown Menu -->
        </ul>
    </div>
</aside>
@push('scripts')
<script>
    $('#sidebarToggle').on('click', function() {
        var sidebar = $('#logo-sidebar');
        var mainSection = $('#main-section');

        sidebar.toggleClass('-translate-x-full');

        if (sidebar.hasClass('-translate-x-full')) {
            sidebar.css({
                'display': 'none',
                'width': '0%'
            });
            mainSection.css('width', '100%');
        } else {
            sidebar.css({
                'display': '',
                'width': ''
            });
            mainSection.css('width', '');
        }
    });
</script>
@endpush