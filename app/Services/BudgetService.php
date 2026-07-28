<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Offer;
use Carbon\Carbon;

class BudgetService
{
    /**
     * Get the budget overview for the current month.
     *
     * @return array
     */
    public function getBudgetOverview(?int $excludeRequestId = null): array
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        // 1. Get the budget for the current month
        $budget = Budget::where('year', $year)->where('month', $month)->first();

        $budgetIqd = $budget->budget_amount_iqd ?? 0;
        $budgetUsd = $budget->budget_amount_usd ?? 0;

        // statuses that count as "Spent" (committed funds)
        $committedStatuses = [
            'Pending Final Payment', // Approved by Finance/Manager
            'Ready to Buy',          // Cash Ready
            'Purchase Logged'        // Bought
        ];

        // 2. Calculate total spending for the current month
        $spentIqd = Offer::where('is_chosen', true)
                                ->whereHas('purchaseRequest', function($q) use ($committedStatuses, $excludeRequestId) {
                                    $q->whereIn('status', $committedStatuses);
                                    if ($excludeRequestId) {
                                        $q->where('id', '!=', $excludeRequestId);
                                    }
                                })
                                ->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->where('currency', 'IQD')
                                ->sum('price');
                                
        $spentUsd = Offer::where('is_chosen', true)
                                ->whereHas('purchaseRequest', function($q) use ($committedStatuses, $excludeRequestId) {
                                    $q->whereIn('status', $committedStatuses);
                                    if ($excludeRequestId) {
                                        $q->where('id', '!=', $excludeRequestId);
                                    }
                                })
                                ->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->where('currency', 'USD')
                                ->sum('price');

        // 3. Calculate remainders
        $remainingIqd = $budgetIqd - $spentIqd;
        $remainingUsd = $budgetUsd - $spentUsd;

        return [
            'budget_iqd' => $budgetIqd,
            'spent_iqd' => $spentIqd,
            'remaining_iqd' => $remainingIqd,
            'budget_usd' => $budgetUsd,
            'spent_usd' => $spentUsd,
            'remaining_usd' => $remainingUsd,
        ];
    }
}
