@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Settings</h1>
</div>

<form action="{{ route('settings.store') }}" method="POST">
    @csrf
    <div class="flex flex-row items-center gap-4">
        <div class="flex flex-col">
            <label for="refer_coin" class="form-label mb-1">Refer Coin</label>
            <input type="number" class="form-control" id="refer_coin" name="refer_coin" value="{{ $setting->refer_coin ?? '' }}" required>
        </div>

        <div class="flex flex-col">
            <label for="welcome_coin" class="form-label mb-1">Welcome Coin</label>
            <input type="number" class="form-control" id="welcome_coin" name="welcome_coin" value="{{ $setting->welcome_coin ?? '' }}" required>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

<form action="{{ route('settings.quiz.save') }}" method="POST" class="mt-4 gap-4 grid">
    <div class="flex justify-between">
        <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Quiz</h1>
    </div>

    @csrf
    <div class="flex flex-row items-center gap-4">
        <div class="flex gap-2">
            <!-- <input type="radio" class="form-control" name="single_language"> -->
            <input type="radio" name="quiz_language_type" value="single" id="quiz_single"> Single Language

            <!-- <label for="single_language" class="form-label mb-0">Single Language</label> -->
        </div>

        <div class="flex gap-2">
            <!-- <input type="radio" class="form-control" name="multi_language"> -->
             <input type="radio" name="quiz_language_type" value="multiple" id="quiz_multiple"> Multi Language

            <!-- <label for="multi_language" class="form-label mb-0">Multi Language</label> -->
        </div>
    </div>

    <div class="flex flex-row items-center gap-4">
        <!-- @foreach($languages as $language)
        <div class="flex gap-2">
            <input type="checkbox" class="form-control" name="english">
            <label for="english" class="form-label mb-0">{{$language->name}}</label>
        </div>
        @endforeach -->

        @foreach($languages as $language)
  <input type="checkbox" name="quiz_languages[]" value="{{ $language->id }}" class="quiz-language-checkbox" data-lang="{{ strtolower($language->name) }}">
  {{ $language->name }}
@endforeach
    </div>

    <div class="flex flex-row items-center gap-4">
        <div>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
            <select id='select_category' name="category_id" class="select_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Choose a Category</option>
                @foreach($categories as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sub Category</label>
            <select id='select_sub_category' name="sub_category_id" class="select_sub_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Choose a Sub Category</option>
                @foreach($sub_categories as $sub_category)
                <option value="{{$sub_category->id}}">{{$sub_category->name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-col">
        <label class="form-label mb-1">Question Limit</label>
        <input type="number" class="form-control w-1/6" name="question_limit" placeholder="Question Limit" required>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<form action="{{ route('settings.cbt.save') }}" method="POST" class="mt-4 gap-4 grid">
    <div class="flex justify-between">
        <h1 class="text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">CBT</h1>
    </div>

    @csrf
    <div class="flex flex-row items-center gap-4">
        <div class="flex gap-2">
            <input type="radio" name="cbt_language_type" value="single" id="cbt_single"> Single Language

            <!-- <input type="radio" class="form-control" name="single_language">
            <label for="single_language" class="form-label mb-0">Single Language</label> -->
        </div>

        <div class="flex gap-2">
            <input type="radio" name="cbt_language_type" value="multiple" id="cbt_multiple"> Multi Language

            <!-- <input type="radio" class="form-control" name="multi_language">
            <label for="multi_language" class="form-label mb-0">Multi Language</label> -->
        </div>
    </div>

    <div class="flex flex-row items-center gap-4">
        <!-- @foreach($languages as $language)
        <div class="flex gap-2">
            <input type="checkbox" class="form-control" name="english">
            <label for="english" class="form-label mb-0">{{$language->name}}</label>
        </div>
        @endforeach -->

        @foreach($languages as $language)
  <input type="checkbox" name="cbt_languages[]" value="{{ $language->id }}" class="cbt-language-checkbox" data-lang="{{ strtolower($language->name) }}">
  {{ $language->name }}
@endforeach
    </div>

    <div class="flex flex-row items-center gap-4">
        <div>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category</label>
            <select id='select_category' name="category_id" class="select_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Choose a Category</option>
                @foreach($categories as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sub Category</label>
            <select id='select_sub_category' name="sub_category_id" class="select_sub_category bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Choose a Sub Category</option>
                @foreach($sub_categories as $sub_category)
                <option value="{{$sub_category->id}}">{{$sub_category->name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
@endsection

@push('scripts')

@include('script')

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const quizRadios = document.querySelectorAll("input[name='quiz_language_type']");
    const quizCheckboxes = document.querySelectorAll(".quiz-language-checkbox");

    function handleQuizSelection() {
        const selectedType = document.querySelector("input[name='quiz_language_type']:checked")?.value;

        quizCheckboxes.forEach(cb => {
            // cb.disabled = false;
            cb.checked = false;
        });

        if (selectedType === "single") {
            quizCheckboxes.forEach(cb => {
                // cb.disabled = false;
                cb.addEventListener("change", singleQuizHandler);
            });
        } else if (selectedType === "multiple") {
            quizCheckboxes.forEach(cb => cb.removeEventListener("change", singleQuizHandler));
            quizCheckboxes.forEach(cb => cb.addEventListener("change", multipleQuizHandler));

            quizCheckboxes.forEach(cb => {
                const lang = cb.dataset.lang;
                if (lang === "english") {
                    cb.checked = true;
                    // cb.disabled = true;
                } else {
                    cb.checked = false;
                    // cb.disabled = false;
                }
            });
        }
    }

    function singleQuizHandler(e) {
        if (e.target.checked) {
            quizCheckboxes.forEach(cb => {
                if (cb !== e.target) cb.checked = false;
            });
        }
    }

    function multipleQuizHandler() {
        const checked = [...quizCheckboxes].filter(cb => cb.checked);
        if (checked.length > 2) {
            alert("Only 1 additional language allowed with English.");
            this.checked = false;
        }
    }

    quizRadios.forEach(radio => {
        radio.addEventListener("change", handleQuizSelection);
    });

    handleQuizSelection();
});

document.addEventListener("DOMContentLoaded", function () {
    const cbtRadios = document.querySelectorAll("input[name='cbt_language_type']");
    const cbtCheckboxes = document.querySelectorAll(".cbt-language-checkbox");

    function handleCBTSelection() {
        const selectedType = document.querySelector("input[name='cbt_language_type']:checked")?.value;

        cbtCheckboxes.forEach(cb => {
            // cb.disabled = false;
            cb.checked = false;
        });

        if (selectedType === "single") {
            cbtCheckboxes.forEach(cb => {
                // cb.disabled = false;
                cb.addEventListener("change", singleCBTHandler);
            });
        } else if (selectedType === "multiple") {
            cbtCheckboxes.forEach(cb => cb.removeEventListener("change", singleCBTHandler));
            cbtCheckboxes.forEach(cb => cb.addEventListener("change", multipleCBTHandler));

            cbtCheckboxes.forEach(cb => {
                const lang = cb.dataset.lang;
                if (lang === "english") {
                    cb.checked = true;
                    // cb.disabled = true;
                } else {
                    cb.checked = false;
                    // cb.disabled = false;
                }
            });
        }
    }

    function singleCBTHandler(e) {
        if (e.target.checked) {
            cbtCheckboxes.forEach(cb => {
                if (cb !== e.target) cb.checked = false;
            });
        }
    }

    function multipleCBTHandler() {
        const checked = [...cbtCheckboxes].filter(cb => cb.checked);
        if (checked.length > 2) {
            alert("Only 1 additional language allowed with English.");
            this.checked = false;
        }
    }

    cbtRadios.forEach(radio => {
        radio.addEventListener("change", handleCBTSelection);
    });

    handleCBTSelection(); 
});

</script>
@endpush