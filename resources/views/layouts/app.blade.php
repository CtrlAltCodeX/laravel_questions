<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Page Title')</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased ">
    <div class="flex">
        @include('layouts.sidebar')

        <div class="pb-5 pt-5 px-10 w-4/5" id='main-section'>
            <div class="flex justify-end mb-6">
                <div class="flex items-center">
                    <div class="flex items-center ms-3 relative">
                        <div>
                            <button
                                type="button"
                                id="profile"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-16 w-16 rounded-full border" src="/dummy-profile.jpg" alt="user photo">
                            </button>
                        </div>
                        <div
                            class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600 absolute top-[20px] right-[2px]"
                            id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{auth()->user()->name}}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{auth()->user()->email}}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="w-full block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                            Sign out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @if(session()->has('success'))
            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                <span class="font-medium">{{session()->get('success')}}</span>
            </div>
            @endif
            @if(session('import_errors'))
            <div class="alert alert-danger">
                <ul>
                    @foreach(session('import_errors') as $error)
                    <li>
                        <h4>Import Errors:</h4> {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if(session()->has('error'))
            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                <span class="font-medium">{{session()->get('error')}}</span>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    @yield('styles')

    @yield('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<!-- Add in <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Add before </body> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // $(document).ready(function() {
        //     $('#profile').on('click', function() {
        //         $("#dropdown-user").toggleClass('hidden');
        //     });
        // });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const profileButton = document.getElementById("profile");
            const dropdownMenu = document.getElementById("dropdown-user");

            // Toggle dropdown on button click
            profileButton.addEventListener("click", function(e) {
                e.stopPropagation(); // Prevent click from propagating to document
                dropdownMenu.classList.toggle("hidden");
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function() {
                dropdownMenu.classList.add("hidden");
            });

            // Prevent dropdown from closing when clicking inside
            dropdownMenu.addEventListener("click", function(e) {
                e.stopPropagation();
            });
        });
    </script>
    @stack("scripts")
</body>

</html>