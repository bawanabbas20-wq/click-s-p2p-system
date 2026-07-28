<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\PurchaseRequest;
use App\Models\Offer;
use App\Models\User;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        // === 1. Enhanced KPI Cards ===
        $totalSpendIqd = Offer::where('is_chosen', true)->where('currency', 'IQD')->sum('price');
        $totalSpendUsd = Offer::where('is_chosen', true)->where('currency', 'USD')->sum('price');

        // Get current month budget
        $currentBudget = Budget::where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        // Calculate budget utilization (avoid division by zero)
        $budgetUtilizationIqd = ($currentBudget && $currentBudget->budget_amount_iqd > 0) 
            ? round(($totalSpendIqd / $currentBudget->budget_amount_iqd) * 100, 1) 
            : 0;
        $budgetUtilizationUsd = ($currentBudget && $currentBudget->budget_amount_usd > 0) 
            ? round(($totalSpendUsd / $currentBudget->budget_amount_usd) * 100, 1) 
            : 0;

        // Average processing time (days)
        $avgProcessingTime = PurchaseRequest::where('status', 'Completed')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days') ?? 0;

        // Success rate (completed vs total requests)
        $totalRequests = PurchaseRequest::count();
        $completedRequests = PurchaseRequest::where('status', 'Completed')->count();
        $successRate = $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 1) : 0;

        // === 2. Budget vs Actual Spending (Bar Chart) ===
        $months = [];
        $budgetDataIqd = [];
        $actualSpendDataIqd = [];
        $budgetDataUsd = [];
        $actualSpendDataUsd = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            // Get budget for this month
            $monthBudget = Budget::where('year', $date->year)
                ->where('month', $date->month)
                ->first();
            
            $budgetAmountIqd = $monthBudget ? $monthBudget->budget_amount_iqd : 0;
            $budgetAmountUsd = $monthBudget ? $monthBudget->budget_amount_usd : 0;
            $budgetDataIqd[] = $budgetAmountIqd;
            $budgetDataUsd[] = $budgetAmountUsd;

            // Get actual spending for this month (IQD)
            $actualSpendIqd = Offer::where('is_chosen', true)
                ->where('currency', 'IQD')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('price');
            $actualSpendDataIqd[] = $actualSpendIqd;

            // Get actual spending for this month (USD)
            $actualSpendUsd = Offer::where('is_chosen', true)
                ->where('currency', 'USD')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('price');
            $actualSpendDataUsd[] = $actualSpendUsd;
        }
        
        $budgetVsActualData = [
            'labels' => $months,
            'iqd' => [
                'budget' => $budgetDataIqd,
                'actual' => $actualSpendDataIqd,
            ],
            'usd' => [
                'budget' => $budgetDataUsd,
                'actual' => $actualSpendDataUsd,
            ]
        ];

        // === 3. Processing Time Analysis (Donut Chart) ===
        $processingTimeRanges = [
            __('1-3 days') => PurchaseRequest::where('status', 'Completed')
                ->whereRaw('DATEDIFF(updated_at, created_at) BETWEEN 1 AND 3')->count(),
            __('4-7 days') => PurchaseRequest::where('status', 'Completed')
                ->whereRaw('DATEDIFF(updated_at, created_at) BETWEEN 4 AND 7')->count(),
            __('8-14 days') => PurchaseRequest::where('status', 'Completed')
                ->whereRaw('DATEDIFF(updated_at, created_at) BETWEEN 8 AND 14')->count(),
            __('15+ days') => PurchaseRequest::where('status', 'Completed')
                ->whereRaw('DATEDIFF(updated_at, created_at) >= 15')->count(),
        ];

        // === 4. Cost Savings Analysis (Donut Chart) ===
        $costSavingsData = PurchaseRequest::join('offers', 'purchase_requests.id', '=', 'offers.purchase_request_id')
            ->where('purchase_requests.status', 'Completed')
            ->where('offers.is_chosen', true)
            ->selectRaw('
                SUM(CASE WHEN offers.price < purchase_requests.estimated_price THEN 1 ELSE 0 END) as under_budget,
                SUM(CASE WHEN offers.price = purchase_requests.estimated_price THEN 1 ELSE 0 END) as on_budget,
                SUM(CASE WHEN offers.price > purchase_requests.estimated_price THEN 1 ELSE 0 END) as over_budget
            ')
            ->first();

        $costSavings = [
            __('Under Budget') => $costSavingsData->under_budget ?? 0,
            __('On Budget') => $costSavingsData->on_budget ?? 0,
            __('Over Budget') => $costSavingsData->over_budget ?? 0,
        ];

        // === 5. Employee Activity Overview ===
        // Fetch all users who have created at least one request
        $employeeOverview = User::whereHas('purchaseRequests')
            ->withCount('purchaseRequests')
            ->get()
            ->map(function ($user) {
                // Calculate spending only for completed requests with chosen offers
                $completedWithOffers = $user->purchaseRequests()
                    ->where('status', 'Completed')
                    ->whereHas('chosenOffer')
                    ->with('chosenOffer')
                    ->get();
                
                $iqdSpending = $completedWithOffers->where('chosenOffer.currency', 'IQD')->sum('chosenOffer.price');
                $usdSpending = $completedWithOffers->where('chosenOffer.currency', 'USD')->sum('chosenOffer.price');

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'total_requests' => $user->purchase_requests_count, // Correct total count
                    'iqd_spending' => $iqdSpending,
                    'usd_spending' => $usdSpending,
                ];
            })
            ->sortByDesc('total_requests')
            ->values();

        // === 6. Purchase Request History ===
        $purchaseRequestHistory = PurchaseRequest::with(['user', 'chosenOffer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'item_name' => $request->item_name,
                    'user_name' => $request->user->name,
                    'status' => $request->status,
                    'estimated_price' => $request->estimated_price,
                    'estimated_currency' => $request->estimated_currency,
                    'final_price' => $request->chosenOffer ? $request->chosenOffer->price : null,
                    'final_currency' => $request->chosenOffer ? $request->chosenOffer->currency : null,
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                    'days_elapsed' => $request->created_at->diffInDays(now()),
                    'status_color' => $this->getStatusColor($request->status),
                ];
            });

        // === 7. Pass all data to the view ===
        return view('analytics.index', [
            // KPI Cards
            'totalSpendIqd' => $totalSpendIqd,
            'totalSpendUsd' => $totalSpendUsd,
            'budgetUtilizationIqd' => $budgetUtilizationIqd,
            'budgetUtilizationUsd' => $budgetUtilizationUsd,
            'avgProcessingTime' => round($avgProcessingTime, 1),
            'successRate' => $successRate,
            
            // Charts
            'budgetVsActualData' => $budgetVsActualData,
            'processingTimeRanges' => $processingTimeRanges,
            'costSavings' => $costSavings,
            
            // Tables
            'employeeOverview' => $employeeOverview,
            'purchaseRequestHistory' => $purchaseRequestHistory,
        ]);
    }

    /**
     * Display purchase history for a specific employee.
     */
    public function employeeHistory(User $user): View
    {
        $requests = $user->purchaseRequests()
            ->with(['chosenOffer', 'offers'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('analytics.employee_history', compact('user', 'requests'));
    }

    /**
     * Get status color for display
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'Completed' => 'text-green-600 bg-green-100',
            'Approved for Purchase' => 'text-blue-600 bg-blue-100',
            'Pending Finance' => 'text-yellow-600 bg-yellow-100',
            'Pending Procurement' => 'text-orange-600 bg-orange-100',
            'Pending Manager' => 'text-purple-600 bg-purple-100',
            'Pending Final Payment' => 'text-yellow-600 bg-yellow-100',
            'Denied' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }
}
