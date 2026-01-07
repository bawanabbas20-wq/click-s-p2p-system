<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase History: ') }} {{ $user->name }}
            </h2>
            <a href="{{ route('analytics.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Back to Analytics
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div class="text-gray-600">
                            Total Requests: <span class="font-semibold">{{ $requests->total() }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">Date Requested</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">Est. Price</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">Final Price</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">View</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ Str::limit($request->item_name, 40) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @switch($request->status)
                                                    @case('Denied') bg-red-100 text-red-800 @break
                                                    @case('Completed') @case('Fulfilled from Stock') bg-green-100 text-green-800 @break
                                                    @case('Approved for Purchase') @case('Purchase Logged') bg-blue-100 text-blue-800 @break
                                                    @case('Pending Final Payment') bg-yellow-100 text-yellow-800 @break
                                                    @default bg-yellow-100 text-yellow-800
                                                @endswitch">
                                                {{ $request->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($request->estimated_currency === 'USD')
                                                ${{ number_format($request->estimated_price, 2) }}
                                            @else
                                                {{ number_format($request->estimated_price, 0) }} IQD
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($request->chosenOffer)
                                                <span class="text-green-600 font-semibold">
                                                @if($request->chosenOffer->currency === 'USD')
                                                    ${{ number_format($request->chosenOffer->price, 2) }}
                                                @else
                                                    {{ number_format($request->chosenOffer->price, 0) }} IQD
                                                @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            {{-- Use approval.show for approvers, regardless of status --}}
                                            @can('is-approver')
                                                <a href="{{ route('approval.show', $request) }}" class="text-brand-green hover:text-green-900">View</a>
                                            @else
                                                <a href="{{ route('requests.show', $request) }}" class="text-brand-green hover:text-green-900">View</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No purchase history found for this user.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
