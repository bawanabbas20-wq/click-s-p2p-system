<x-app-layout>
    <x-slot name="header">
        {{ __('Analytics Dashboard') }}
    </x-slot>

    <style>
        /* Prevent layout shifts and improve scroll performance */
        canvas {
            display: block;
            box-sizing: border-box;
            height: 300px !important;
            width: 100% !important;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            contain: layout style paint;
        }
        
        /* Optimize scrolling performance */
        * {
            -webkit-backface-visibility: hidden;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
        }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Budget Utilization IQD -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Budget Utilization (IQD)') }}</h3>
                <p class="mt-1 text-3xl font-semibold {{ $budgetUtilizationIqd > 90 ? 'text-red-600' : ($budgetUtilizationIqd > 75 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $budgetUtilizationIqd }}%
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($totalSpendIqd, 0) }} IQD spent</p>
            </div>
        </div>

        <!-- Budget Utilization USD -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Budget Utilization (USD)') }}</h3>
                <p class="mt-1 text-3xl font-semibold {{ $budgetUtilizationUsd > 90 ? 'text-red-600' : ($budgetUtilizationUsd > 75 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $budgetUtilizationUsd }}%
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${{ number_format($totalSpendUsd, 2) }} spent</p>
            </div>
        </div>

        <!-- Average Processing Time -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Avg Processing Time') }}</h3>
                <p class="mt-1 text-3xl font-semibold text-blue-600">{{ $avgProcessingTime }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('days to complete') }}</p>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('Request Success Rate') }}</h3>
                <p class="mt-1 text-3xl font-semibold {{ $successRate > 80 ? 'text-green-600' : ($successRate > 60 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $successRate }}%
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('completion rate') }}</p>
            </div>
        </div>
        <!-- Budget vs Actual Spending -->
        <div class="col-span-1 md:col-span-2 lg:col-span-4 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 sm:mb-0">{{ __('Budget vs Actual Spending (Last 6 Months)') }}</h3>
                    <div class="flex items-center space-x-2">
                        <label for="currencySelect" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Currency:') }}</label>
                        <select id="currencySelect" class="block w-20 px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-brand-green focus:border-brand-green bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onchange="switchCurrency()">
                            <option value="IQD" selected>IQD</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                </div>
                @if(collect($budgetVsActualData['iqd']['actual'])->sum() > 0 || collect($budgetVsActualData['usd']['actual'])->sum() > 0)
                    <div class="chart-container mt-4">
                        <canvas id="budgetVsActualChart"></canvas>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500">
                        No spending data to display yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- Processing Time Analysis -->
        <div class="col-span-1 md:col-span-1 lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Processing Time Analysis') }}</h3>
                @if(array_sum($processingTimeRanges) > 0)
                    <div class="chart-container mt-4">
                        <canvas id="processingTimeChart"></canvas>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500">
                        No completed requests to analyze.
                    </div>
                @endif
            </div>
        </div>

        <!-- Cost Savings Analysis -->
        <div class="col-span-1 md:col-span-1 lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Cost Savings Analysis') }}</h3>
                @if(array_sum($costSavings) > 0)
                    <div class="chart-container mt-4">
                        <canvas id="costSavingsChart"></canvas>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64 text-gray-500">
                        No cost data to analyze yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Spending Users -->
        <div class="col-span-1 md:col-span-2 lg:col-span-4 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Employee Activity Overview') }}</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('User') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Total Requests') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('IQD Spending (Completed)') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('USD Spending (Completed)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($employeeOverview as $user)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('analytics.employee', $user['id']) }}" class="text-brand-green hover:underline">
                                            {{ $user['name'] }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $user['total_requests'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                        @if($user['iqd_spending'] > 0)
                                            {{ number_format($user['iqd_spending'], 0) }} IQD
                                        @else
                                            <span class="text-gray-400">0 IQD</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                        @if($user['usd_spending'] > 0)
                                            ${{ number_format($user['usd_spending'], 2) }}
                                        @else
                                            <span class="text-gray-400">$0.00</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">No spending data available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Purchase Request History - Last Card (Full Width) -->
        <div class="col-span-1 md:col-span-2 lg:col-span-4 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Recent Purchase Request History') }}</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Item') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('User') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Price') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Days') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Date Created') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($purchaseRequestHistory as $request)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('requests.show', $request['id']) }}" class="text-brand-green hover:text-opacity-80">
                                            {{ Str::limit($request['item_name'], 40) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $request['user_name'] }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request['status_color'] }}">
                                            {{ __($request['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($request['final_price'])
                                            <div class="text-green-600 font-semibold">
                                                {{ number_format($request['final_price'], 2) }} {{ $request['final_currency'] }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                Est: {{ number_format($request['estimated_price'], 2) }} {{ $request['estimated_currency'] }}
                                            </div>
                                        @else
                                            <div class="text-gray-500">
                                                {{ number_format($request['estimated_price'], 2) }} {{ $request['estimated_currency'] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request['days_elapsed'] }}d
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request['created_at']->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-sm text-gray-500 text-center">No purchase requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Throttle function to limit resize events
        function throttle(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Budget vs Actual Spending Chart (Bar)
            @if(collect($budgetVsActualData['iqd']['actual'])->sum() > 0 || collect($budgetVsActualData['usd']['actual'])->sum() > 0)
                const budgetVsActualData = @json($budgetVsActualData);
                let budgetChart;
                
                function createBudgetChart(currency) {
                    const ctx = document.getElementById('budgetVsActualChart');
                    
                    // Destroy existing chart if it exists
                    if (budgetChart) {
                        budgetChart.destroy();
                    }
                    
                    const currencyData = budgetVsActualData[currency.toLowerCase()];
                    const currencySymbol = currency === 'USD' ? '$' : '';
                    const currencyLabel = currency;
                    
                    budgetChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: budgetVsActualData.labels,
                            datasets: [{
                                label: `{{ __('Budget') }} (${currencyLabel})`,
                                data: currencyData.budget,
                                backgroundColor: 'rgba(59, 130, 246, 0.5)', // Blue
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }, {
                                label: `{{ __('Actual Spend') }} (${currencyLabel})`,
                                data: currencyData.actual,
                                backgroundColor: 'rgba(34, 197, 94, 0.5)', // Brand Green
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: { 
                            scales: { 
                                y: { 
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return currencySymbol + value.toLocaleString();
                                        }
                                    }
                                } 
                            }, 
                            responsive: true, 
                            maintainAspectRatio: false,
                            animation: { duration: 300 },
                            interaction: { intersect: false },
                            plugins: { 
                                legend: { display: true },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + currencySymbol + context.parsed.y.toLocaleString();
                                        }
                                    }
                                }
                            },
                            onResize: throttle(function(chart, size) {
                                chart.resize();
                            }, 100)
                        }
                    });
                }
                
                // Initialize with IQD
                createBudgetChart('IQD');
                
                // Currency switching function
                window.switchCurrency = function() {
                    const selectedCurrency = document.getElementById('currencySelect').value;
                    createBudgetChart(selectedCurrency);
                };
            @endif

            // 2. Processing Time Analysis Chart (Doughnut)
            @if(array_sum($processingTimeRanges) > 0)
                const processingTimeData = @json($processingTimeRanges);
                new Chart(document.getElementById('processingTimeChart'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(processingTimeData),
                        datasets: [{
                            data: Object.values(processingTimeData),
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.7)',   // Green for 1-3 days
                                'rgba(234, 179, 8, 0.7)',   // Yellow for 4-7 days
                                'rgba(249, 115, 22, 0.7)',  // Orange for 8-14 days
                                'rgba(239, 68, 68, 0.7)'    // Red for 15+ days
                            ],
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        animation: { duration: 0 },
                        interaction: { intersect: false },
                        plugins: { legend: { position: 'bottom' } },
                        onResize: throttle(function(chart, size) {
                            chart.resize();
                        }, 100)
                    }
                });
            @endif

            // 3. Cost Savings Analysis Chart (Doughnut)
            @if(array_sum($costSavings) > 0)
                const costSavingsData = @json($costSavings);
                new Chart(document.getElementById('costSavingsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(costSavingsData),
                        datasets: [{
                            data: Object.values(costSavingsData),
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.7)',   // Green for Under Budget
                                'rgba(59, 130, 246, 0.7)',  // Blue for On Budget
                                'rgba(239, 68, 68, 0.7)'    // Red for Over Budget
                            ],
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        animation: { duration: 0 },
                        interaction: { intersect: false },
                        plugins: { legend: { position: 'bottom' } },
                        onResize: throttle(function(chart, size) {
                            chart.resize();
                        }, 100)
                    }
                });
            @endif
        });
    </script>
</x-app-layout>
