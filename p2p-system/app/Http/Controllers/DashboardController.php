<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Employee Stats
        $totalRequests = $user->purchaseRequests()->count();
        
        $pendingRequests = $user->purchaseRequests()
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Rejected')
            ->count();
            
        $completedRequests = $user->purchaseRequests()
            ->where('status', 'Completed')
            ->count();

        // Calculate breakdown of pending requests
        $pendingBreakdown = $user->purchaseRequests()
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Rejected')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Recent Activity
        // Recent Activity (with Filters)
        $query = $user->purchaseRequests()->latest();

        if (request('search')) {
            $search = request('search');
            $query->where('item_name', 'like', "%{$search}%");
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $recentRequests = $query->paginate(10, ['*'], 'recent_page')->withQueryString();

        return view('dashboard', compact(
            'totalRequests', 
            'pendingRequests', 
            'completedRequests', 
            'recentRequests',
            'pendingBreakdown'
        ));
    }
}
