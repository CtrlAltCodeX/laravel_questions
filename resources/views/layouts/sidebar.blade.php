<!-- use Illuminate\Support\Facades\Route; -->
@php
use Illuminate\Support\Str;
@endphp

<style>
    #logo-sidebar {
        transition: width 0.3s ease;
    }

    #logo-sidebar.sidebar-expanded {
        width: 16rem;
        /* Expanded width */
    }

    #logo-sidebar.sidebar-minimized {
        width: auto;
        /* Minimized width */
    }

    #logo-sidebar.sidebar-minimized .sidebar-text {
        display: none;
        /* Hide text in minimized state */
    }

    #logo-sidebar.sidebar-minimized .flex {
        justify-content: center;
        /* Center the icons */
    }

    .dropdown {
        display: none;
        transition: all 0.2s ease-in-out;
    }

    li {
        border: 1px solid #ccc;
    }

    .dropdown a {
        border: 1px solid #ccc;
    }
</style>

<aside id="logo-sidebar" class="min-h-screen w-1/5 bg-gray-100 dark:bg-gray-900 w-64 h-screen pt-5 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 sidebar-expanded" aria-label="Sidebar">
    <div class="h-full px-2 overflow-y-auto bg-white dark:bg-gray-800 sidebar">
        <div class="flex justify-between">
            <a href="https://flowbite.com" class="flex items-center shadow p-2 logo">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
                <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Flowbite</span>
            </a>
            <button id="sidebarToggle" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Toggle sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>
        </div>

        <ul class="space-y-2 font-medium mt-4">
            @foreach ($menuItems as $item)
            @if(!isset($item['sub-menus']))
            <li>
                <a href="{{ route($item['route']) }}"
                    class="block px-4 py-2 rounded {{ Str::startsWith(url()->current(),route($item['route'])) ? 'bg-gray-900 text-white':'hover:bg-gray-200'}}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span class="ms-3 sidebar-text">{{$item['name']}}</span>
                </a>
            </li>
            @else
            <div class="mt-2">
                <button class="flex justify-between items-center w-full px-4 py-2 rounded border">
                    <div class="flex gap-2 items-center">
                        <i class="{{ $item['icon'] }}"></i>
                        <span class="sidebar-text">{{$item['name']}}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <!-- Dropdown Content -->
                <div class="mt-2 space-y-2 pl-14 dropdown" style="background-color: #eee;">
                    @foreach($item['sub-menus'] as $subMenu)
                    <a href="{{ route($subMenu['route'], ['sort'=>'id', 'direction' => 'asc', 'data' => 10]) }}"
                        class="block px-2 py-2 rounded {{ Str::startsWith(url()->current(),route($subMenu['route'])) ? 'bg-gray-900 text-white':'hover:bg-gray-200'}}">

                        <i class="{{ $subMenu['icon'] }}"></i>
                        <span class="sidebar-text">{{$subMenu['name']}}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </ul>
    </div>
</aside>


@push('scripts')
<script>
    $(document).ready(function() {
        var sidebar = $('#logo-sidebar');
        var mainSection = $('#main-section');

        // Check local storage for sidebar state
        var sidebarState = localStorage.getItem('sidebarState');
        if (sidebarState === 'minimized') {
            sidebar.addClass('sidebar-minimized').removeClass('sidebar-expanded');
            $(".logo").addClass('hidden');
            mainSection.css('width', 'calc(100% - 4rem)');
            $('.sidebar').addClass('overflow-hidden');
            $('.dropdown').removeClass('pl-14');
            $('.dropdown a').addClass('text-center');
            $('ul li a').addClass('text-center');
        }

        // Sidebar toggle
        $('#sidebarToggle').on('click', function() {
            sidebar.toggleClass('sidebar-expanded sidebar-minimized');
            if (sidebar.hasClass('sidebar-minimized')) {
                localStorage.setItem('sidebarState', 'minimized');
                $(".logo").addClass('hidden');
                mainSection.css('width', 'calc(100% - 4rem)');
                $('.sidebar').addClass('overflow-hidden');
                $('.dropdown').removeClass('pl-14');
                $('.dropdown a').addClass('text-center');
                $('ul li a').addClass('text-center');
            } else {
                localStorage.setItem('sidebarState', 'expanded');
                $(".logo").removeClass('hidden');
                mainSection.css('width', 'calc(100% - 16rem)');
                $('.sidebar').removeClass('overflow-hidden');
                $('.dropdown').addClass('pl-14');
                $('.dropdown a').removeClass('text-center');
                $('ul li a').removeClass('text-center');
            }
        });

        // Dropdown toggle
        $('.dropdown').prev('button').on('click', function() {
            var dropdown = $(this).next('.dropdown');
            dropdown.slideToggle(200);
            $(this).find('svg').toggleClass('rotate-180');
            $('.dropdown').not(dropdown).slideUp(200);
            $('.dropdown').not(dropdown).prev('button').find('svg').removeClass('rotate-180');
        });

        // Open dropdowns for current route
        var currentUrl = new URL(window.location.href).pathname; // Get the base path without query params

        // Loop through each dropdown
        $('.dropdown').each(function() {
            var parentButton = $(this).prev('button');
            var relatedRoutes = [];

            // Collect all related routes (sub-menus)
            $(this).find('a').each(function() {
                var routePath = new URL($(this).attr('href'), window.location.origin).pathname; // Get base path
                relatedRoutes.push(routePath);
            });

            // Check if current URL matches any related route
            if (relatedRoutes.some(route => currentUrl === route)) {
                $(this).slideDown(200); // Open dropdown
                parentButton.find('svg').addClass('rotate-180'); // Rotate arrow
            }
        });
    });
</script>
@endpush