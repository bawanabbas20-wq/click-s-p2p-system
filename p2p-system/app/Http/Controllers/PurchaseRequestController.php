<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Requests\StorePurchaseRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\PurchaseRequest;
use App\Models\RequestLog;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Notifications\NewRequestForApprovalNotification;
use Illuminate\Support\Facades\Notification;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the user's purchase requests.
     */
    public function index(): View
    {
        // Get the currently logged-in user
        $user = Auth::user();

        // Load the user's purchase requests, ordered by the most recent
        $requests = $user->purchaseRequests()
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        // We will create this view in the next step
        return view('requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // We will create this view in the next step
        return view('requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // 1. Determine the correct initial status
        $newStatus = 'Pending Procurement';
        if ($user->role === 'procurement') {
            $newStatus = 'Pending Finance'; // Auto-escalate
        }

        // 2. Create the purchase request
        $purchaseRequest = PurchaseRequest::create([
            'user_id' => $user->id,
            'item_name' => $request->item_name,
            'estimated_price' => $request->estimated_price,
            'estimated_currency' => $request->estimated_currency,
            'date_wanted' => $request->date_wanted,
            'justification' => $request->justification,
            'status' => $newStatus,
            'priority' => $request->priority,
        ]);

        // 3. Create the initial log entry
        $logComment = 'Request submitted by employee.';
        if ($newStatus === 'Pending Finance') {
            $logComment = 'Auto-escalated: Requester is in Procurement.';
        }

        RequestLog::create([
            'purchase_request_id' => $purchaseRequest->id,
            'user_id' => $user->id,
            'old_status' => 'New',
            'new_status' => $newStatus,
            'comment' => $logComment,
        ]);

        // 4. Notify the correct group
        $adminUsers = User::where('role', 'admin')->get(); // Get Admins

        if ($newStatus === 'Pending Finance') {
            $financeUsers = User::where('role', 'finance')->get();
            Notification::send($financeUsers, new NewRequestForApprovalNotification($purchaseRequest, 'Finance'));
            Notification::send($adminUsers, new NewRequestForApprovalNotification($purchaseRequest, 'Finance (for Admin)')); // Notify Admin
        } else {
            $procurementUsers = User::where('role', 'procurement')->get();
            Notification::send($procurementUsers, new NewRequestForApprovalNotification($purchaseRequest, 'Procurement'));
            Notification::send($adminUsers, new NewRequestForApprovalNotification($purchaseRequest, 'Procurement (for Admin)')); // Notify Admin
        }

        // 5. Notify the requester that their request was received
        $user->notify(new \App\Notifications\RequestSubmittedNotification($purchaseRequest));
        
        return redirect()->route('requests.index')->with('success', 'Purchase request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseRequest $purchaseRequest): View
    {
        // Authorize: Make sure the user is the creator OR a manager/finance/admin.
        $user = auth()->user();
        
        // We re-use the 'can-manage-budgets' Gate (admin, finance, manager)
        if ($user->id !== $purchaseRequest->user_id && !$user->can('can-manage-budgets')) {
            abort(403);
        }

        // Eager load the related logs and the user who wrote each log, plus parent/child relationships
        $purchaseRequest->load('requestLogs.user', 'offers', 'chosenOffer', 'parentRequest', 'childRequests');

        return view('requests.show', compact('purchaseRequest'));
    }

    /**
     * Mark the specified request as completed by the employee.
     */
    public function confirm(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        // Authorize: Make sure the user confirming is the one who created it.
        if (auth()->id() !== $purchaseRequest->user_id) {
            abort(403);
        }

        // Only allow confirmation if the status is 'Purchase Logged' or 'Fulfilled from Stock'
        if (!in_array($purchaseRequest->status, ['Purchase Logged', 'Fulfilled from Stock'])) {
            return back()->with('error', 'This request cannot be confirmed at this time.');
        }

        // Update the status
        $purchaseRequest->update(['status' => 'Completed']);
        
        // (Optional) We could add a final 'RequestLog' entry here

        return redirect()->route('requests.index')->with('success', 'Request marked as completed. Thank you!');
    }

    /**
     * Clone a denied request for resubmission
     */
    public function resubmit(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        // Check if user owns the request
        if ($purchaseRequest->user_id !== auth()->id()) {
            return redirect()->route('requests.index')->with('error', 'You can only resubmit your own requests.');
        }

        // Check if request is denied
        if ($purchaseRequest->status !== 'Denied') {
            return redirect()->route('requests.show', $purchaseRequest)->with('error', 'Only denied requests can be resubmitted.');
        }

        // Clone the request with updated date (next month)
        $newRequest = PurchaseRequest::create([
            'user_id' => $purchaseRequest->user_id,
            'parent_request_id' => $purchaseRequest->id,
            'item_name' => $purchaseRequest->item_name,
            'estimated_price' => $purchaseRequest->estimated_price,
            'estimated_currency' => $purchaseRequest->estimated_currency,
            'date_wanted' => now()->addMonth()->format('Y-m-d'), // Set to next month
            'justification' => $purchaseRequest->justification . "\n\n[Resubmitted from request #{$purchaseRequest->id}]",
            'status' => 'Pending Manager',
            'priority' => $purchaseRequest->priority,
        ]);

        // Create initial log entry for the new request
        RequestLog::create([
            'purchase_request_id' => $newRequest->id,
            'user_id' => auth()->id(),
            'old_status' => null,
            'new_status' => 'Pending Manager',
            'comment' => "Request resubmitted from original request #{$purchaseRequest->id}",
        ]);

        // Send notification to manager
        $manager = User::where('role', 'manager')->first();
        if ($manager) {
            $manager->notify(new NewRequestForApprovalNotification($newRequest, 'Manager'));
        }

        return redirect()->route('requests.show', $newRequest)->with('success', 'Request has been resubmitted successfully! The new request is scheduled for next month.');
    }
}
