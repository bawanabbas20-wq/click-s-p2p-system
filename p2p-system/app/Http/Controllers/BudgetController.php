<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class BudgetController extends Controller
{
    /**
     * Display the budget management form for a given year.
     */
    public function index(Request $request): View
    {
        // Default to the current year, but allow viewing other years
        $year = $request->input('year', Carbon::now()->year);

        // Get all budgets for the selected year and key them by month for easy access
        $budgets = Budget::where('year', $year)
                         ->get()
                         ->keyBy('month');

        // Create an array of month numbers (1-12)
        $months = range(1, 12);

        // Get exchange rate setting
        $exchangeRate = Setting::where('key', 'exchange_rate_usd_to_iqd')->first();

        return view('budgets.index', compact('year', 'budgets', 'months', 'exchangeRate'));
    }

    /**
     * Store or update the budgets for a given year.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the incoming data
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2099',
            'iqd' => 'required|array',
            'iqd.*' => 'nullable|numeric|min:0', // Validate each item in the iqd array
            'usd' => 'required|array',
            'usd.*' => 'nullable|numeric|min:0', // Validate each item in the usd array
            'exchange_rate_usd_to_iqd' => 'nullable|numeric|min:0', // Add this
        ]);

        $year = $validated['year'];

        // Loop through all 12 months and update or create the budget entry
        for ($month = 1; $month <= 12; $month++) {
            Budget::updateOrCreate(
                [
                    'year' => $year,
                    'month' => $month
                ],
                [
                    'budget_amount_iqd' => $validated['iqd'][$month] ?? 0,
                    'budget_amount_usd' => $validated['usd'][$month] ?? 0,
                ]
            );
        }

        // Add this block after the for loop, before the return
        Setting::updateOrCreate(
            ['key' => 'exchange_rate_usd_to_iqd'],
            ['value' => $validated['exchange_rate_usd_to_iqd'] ?? 1450] // Default 1450
        );

        return redirect()->route('budgets.index', ['year' => $year])
                         ->with('success', __('Budgets for :year updated successfully!', ['year' => $year]));
    }
}
