@extends('layouts.app')

@section('title', 'Wallet History')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">Wallet History</h1>

    <div class="flex justify-end items-center gap-2">
        <input type="text" id="searchFilter" placeholder="Search Offers..." class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">
    </div>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
    <form action="{{ route('reports.index') }}" method="GET">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="offersTable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Id</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Star</th>
                    <th scope="col" class="px-6 py-3">Method</th>
                    <th scope="col" class="px-6 py-3">Amount</th>
                    <th scope="col" class="px-6 py-3">Tr ID</th>
                    <th scope="col" class="px-6 py-3">Payment Type</th>
                    <th scope="col" class="px-6 py-3">Date/Time</th> <!-- FIXED COLUMN -->
                    <th scope="col" class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($walletHistories as $walletHistorie)
                <tr class="offerRow odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->id}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->user->name}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->coin}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->method}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->amount}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->transaction_id}}
                    </th>

                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$walletHistorie->payment_type}}
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ \Carbon\Carbon::parse($walletHistorie->date)->format('d/m/Y H:i A') }} <!-- FIXED DATE FORMAT -->
                    </th>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                       

                        {{$walletHistorie->status}}
                    </th>
                </tr>
                @empty
                <tr>
                    <td colspan="8" align="center">No Result Found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    @if(request()->data != 'all')
    <div class="flex justify-between items-center">
        <div style="width: 92%;">
            {{ $walletHistories->appends(request()->query())->links() }} <!-- FIXED VARIABLE NAME -->
        </div>
        <div>
            <a href="{{ request()->fullUrlWithQuery(['data' => 'all']) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                View All
            </a>
        </div>
    </div>
    @endif
</div>

@endsection
