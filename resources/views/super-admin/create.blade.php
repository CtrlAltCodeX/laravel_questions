@extends('layouts.app')

@section('content')

<div class="flex justify-between">
    <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Create Admin </h1>

</div>

<div class="py-6">
    <form action="{{ route('super-admin.store') }}" method="POST">
        @csrf
        <!-- @method("PUT") -->
        <div class="max-w-sm mb-5">
            <label for="name" class="block mb-6 text-sm font-medium text-gray-900 dark:text-white">Name</label>
            <input type="text" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500  w-64 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="name" value="" />

            @error('name')
            <div class="text-red-600 text-sm">{{$message}}</div>
            @enderror

        </div>

        <div class="max-w-sm mb-5">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
            <input type="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-64 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="email" value="{{ old('') }}" autocomplete="off" placeholder="Enter email...." />
            @error('email')
            <div class="text-red-600 text-sm">{{$message}}</div>
            @enderror


        </div>



        <div class="mb-5 max-w-sm">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
            <input type="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="password" autocomplete="off" placeholder="Enter password...." />
            @error('password')
            <div class="text-red-600 text-sm">{{$message}}</div>
            @enderror
        </div>


        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-4">Create</button>
    </form>
</div>

@endsection