@extends('layouts.app')

@section('content')
<div class="flex justify-between">
<h1 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-3xl dark:text-white text-left">Create Category</h1>
</div>
<form class="max-w-sm mx-auto" method="POST" action="{{ route('category.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3 relative" style="height: 100px;">
        <div class="container">
            <input accept="image/*" type="file" class="opacity-0 w-[100] h-[100] absolute z-10 cursor-pointer" name="photo" style="width: 100px; height:100px;" id='fileInput' />
            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white image" src="/dummy.jpg" alt="" id='categoryImage' style='width:100px;height:100px;'>
            <div class="bg-black/[0.5] overlay absolute h-[100%] top-[0px] w-[100px] rounded-full opacity-0 flex justify-center items-center text-white">Upload Pic</div>
        </div>
    </div>
    @error('photo')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <div class="mb-5">
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
        <input type="text" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="name" required />
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mx-auto mb-5">
        <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Language</label>
        <select name="language_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">Choose a language</option>
            @foreach($languages as $language)
            <option value="{{$language->id}}">{{$language->name}}</option>
            @endforeach
        </select>
        @error('language_id')
        <div class="text-red-500">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
</form>

@endsection

@section('scripts')
<script>
document.getElementById('fileInput').addEventListener('change', function(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('categoryImage');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
});
</script>
@endsection