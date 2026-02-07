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

        <div class="flex flex-col w-full md:w-1/3">
            <label for="fcm_server_key" class="form-label mb-1">FCM Server Key</label>
            <textarea class="form-control" id="fcm_server_key" name="fcm_server_key" rows="1">{{ $setting->fcm_server_key ?? '' }}</textarea>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
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