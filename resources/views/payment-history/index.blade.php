@extends('layouts.app')

@section('title', 'Payment History')

@section('content')

<div class="flex justify-between">
    <h1 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-white">
        Payment History
    </h1>

    <div class="flex justify-end items-center gap-2">
        <input type="text" id="searchFilter" placeholder="Search Payments..."
            class="border border-gray-300 rounded-lg text-sm px-4 py-2 dark:bg-gray-700 dark:text-white">

        <a href="{{ route('payments.export') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded">
            Export
        </a>
    </div>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg space-y-5">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" id="paymentsTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">User Name</th>
                <th scope="col" class="px-6 py-3">Course Name</th>
                     <th scope="col" class="px-6 py-3">Email</th>
                <th scope="col" class="px-6 py-3">Amount / Contact</th>
                <th scope="col" class="px-6 py-3">Currency</th>
                <th scope="col" class="px-6 py-3">Status</th>
           

                <th scope="col" class="px-6 py-3">Date/Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr class="paymentRow odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment->id }}
                </th>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment->user->name ?? '-' }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment->course->name ?? '-' }}
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment->email }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-600"> {{ $payment->amount ?? '-' }}</span>
                        <span>{{ $payment->contact ?? '-' }}</span>
                    </div>
                </td>


                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment->currency }}
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <span class="">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>


                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y h:i A') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" align="center" class="py-4">No Result Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>

@endsection


@push('scripts')

<script>
    document.getElementById('searchFilter').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        document.querySelectorAll('.paymentRow').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endpush