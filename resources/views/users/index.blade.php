@extends('layouts.app')
@section('title', 'Users')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">User List</h1>

</div>

<div class="relative overflow-x-auto sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mb-4">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Email
                </th>
                <!-- <th scope="col" class="px-6 py-3">
                    Action
                </th> -->
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$user->name}}
                </th>

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$user->email}}
                </th>

                <!-- <td class="px-6 py-4 flex gap-4">
                    @if(Auth::user()->id !== $user->id && Auth::user()->role === "Super admin")
                    <form action="{{route('profile.destroy',$user->id)}}" method='POST'>
                        @csrf
                        @method('DELETE')
                        <button href="#" class="font-medium text-danger dark:text-danger-500 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    @endif
                    @if(Auth::user()->isSuperAdmin())
                    <a href="{{route('profile.edit',$user->id)}}" class="font-medium text-danger dark:text-danger-500 hover:underline">Edit</a>
                    @endif
                </td> -->
            </tr>
            @empty
            <tr>
                <td colspan="3" align="center">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
</div>

@endsection