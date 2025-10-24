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
                   <th scope="col" class="px-6 py-3">Source</th>
                <th scope="col" class="px-6 py-3">User Name / Email</th>
                <th scope="col" class="px-6 py-3">Course Name</th>
                <th scope="col" class="px-6 py-3">Contact</th>
                <th scope="col" class="px-6 py-3">Amount / Currency</th>
                <th scope="col" class="px-6 py-3">Payment id</th>

                <th scope="col" class="px-6 py-3">Method / Card Network</th>
                <th scope="col" class="px-6 py-3">Card last4</th>
                <th scope="col" class="px-6 py-3">VPA</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Date / Time</th>
            </tr>
        </thead>

        <tbody>
            @forelse($payments as $payment)
            <tr class="paymentRow odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['id'] }}
                </th>
 <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['source'] }}
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-700">{{ $payment['user_name'] }}</span>
                        <span class="text-gray-500">{{ $payment['email'] }}</span>
                    </div>
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['course_name'] }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['contact'] }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-700">{{ $payment['amount'] }}</span>
                        <span class="text-gray-500">{{ $payment['currency'] }}</span>
                    </div>
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['payment_id'] }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="flex flex-col">
                        <span class="font-semibold text-gray-700">
                            {{ $payment['method'] }}
                        </span>

                        <span class="text-gray-500 text-xs">
                          {{ $payment['card_network'] }}
                        </span>

                    </div>
                </td>


                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['card_last4'] }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['vpa'] }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $payment['status'] }}
                </td>

                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ \Carbon\Carbon::parse($payment['created_at'])->format('d/m/Y h:i A') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" align="center" class="py-4">No Result Found</td>
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