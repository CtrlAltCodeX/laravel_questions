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

@endsection