<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Offer;
use App\Models\RequestLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OfferController extends Controller
{
    /**
     * Display a listing of requests ready for purchase.
     */
    /**
     * Display a listing of requests that need QUOTATIONS.
     */
    public function index(Request $request): View
    {
        if (!auth()->user()->can('is-procurement') && !auth()->user()->can('is-admin')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $query = PurchaseRequest::with('user')
                                ->where('status', 'Approved for Purchase');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('date_wanted', 'asc')
                          ->paginate(10);
        
        return view('offers.index', compact('requests'));
    }

    /**
     * Display a listing of requests that are READY TO BUY (Cash Ready).
     */
    public function readyToBuy(Request $request): View
    {
        if (!auth()->user()->can('is-procurement') && !auth()->user()->can('is-admin')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $query = PurchaseRequest::with(['user', 'chosenOffer'])
                                ->where('status', 'Ready to Buy');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('updated_at', 'desc')
                          ->paginate(10);
        
        return view('offers.ready_to_buy', compact('requests'));
    }

    /**
     * Show the form for creating a new offer (and listing existing ones).
     */
    public function create(PurchaseRequest $purchaseRequest): View
    {
        if (!auth()->user()->can('is-procurement') && !auth()->user()->can('is-admin')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        // Ensure the request is actually approved
        if ($purchaseRequest->status !== 'Approved for Purchase') {
            abort(403, 'This request is not approved for purchase.');
        }
        
        $purchaseRequest->load('offers');
        $vendors = \App\Models\Vendor::orderBy('name')->get();

        // Check for rejection feedback (Look for the most recent log where status became 'Approved for Purchase' coming from something else)
        $rejectionLog = $purchaseRequest->requestLogs()
            ->with('user')
            ->where('new_status', 'Approved for Purchase')
            ->where('old_status', '!=', 'Approved for Purchase')
            ->where('old_status', '!=', 'Pending Procurement') // Fix: Don't show "Moved to Needs Quotations" as a rejection
            ->whereNotNull('comment')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return view('offers.create', compact('purchaseRequest', 'vendors', 'rejectionLog'));
    }

    /**
     * Store a new offer (quotation).
     */
    public function store(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!auth()->user()->can('is-procurement') && !auth()->user()->can('is-admin')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        // 1. Validate the form data
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'in:IQD,USD'],
            'quotation_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'], // 2MB Max
        ]);

        // 2. Handle the file upload
        $filePath = null;
        if ($request->hasFile('quotation_file')) {
            $filePath = $request->file('quotation_file')->store('quotations', 'public');
        }

        $vendor = \App\Models\Vendor::find($validated['vendor_id']);

        // 3. Create the Offer record
        Offer::create([
            'purchase_request_id' => $purchaseRequest->id,
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->name, // Keep for historical/display ease
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'quotation_file_path' => $filePath,
            'is_chosen' => false,
        ]);

        return redirect()->route('offers.create', $purchaseRequest)->with('success', 'Offer added successfully.');
    }

    /**
     * Submit a recommendation (Procurement Action).
     * Handles both Low Value (<100k: Direct to Finance for Cash) and High Value (>=100k: Finance -> Manager).
     */
    public function submitRecommendation(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!auth()->user()->can('is-procurement') && !auth()->user()->can('is-admin')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'selected_offer_id' => ['required', 'exists:offers,id'],
            'recommendation_reason' => ['required', 'string', 'max:1000'],
        ]);

        $offer = Offer::findOrFail($validated['selected_offer_id']);

        // 1. Ensure this offer belongs to this request
        if ($purchaseRequest->id !== $offer->purchase_request_id) {
            abort(404, 'Offer does not belong to this request.');
        }

        // 2. Set all other offers to 'is_chosen = false'
        $purchaseRequest->offers()->update(['is_chosen' => false, 'is_procurement_recommended' => false]);
        
        // 3. Set the selected offer
        $offer->is_chosen = true;
        $offer->is_procurement_recommended = true;
        $offer->procurement_recommendation_reason = $validated['recommendation_reason'];
        $offer->save();

        // 4. Update the main request status
        $oldStatus = $purchaseRequest->status;
        $newStatus = 'Pending Finance'; 
        
        $purchaseRequest->status = $newStatus;
        $purchaseRequest->save();

        // 5. Determine if High or Low Value for logging context (Logic is in Model)
        $isHighValue = $offer->isHighValue();
        $logComment = $isHighValue 
            ? 'Offer selected (High Value). Escalated to Finance for budget check and further approval.' 
            : 'Offer selected (Low Value). Sent to Finance for cash confirmation.';

        // 6. Create a log entry
        RequestLog::create([
            'purchase_request_id' => $purchaseRequest->id,
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $logComment,
        ]);

        // 7. Notify Finance
        $financeUsers = \App\Models\User::where('role', 'finance')->get();
        \Illuminate\Support\Facades\Notification::send($financeUsers, new \App\Notifications\NewRequestForApprovalNotification($purchaseRequest, 'Final Approval (Finance)'));
        
        return redirect()->route('offers.index')->with('success', 'Offer selected. Request sent to Finance.');
    }

    /**
     * Select a final offer (Legacy/Simple).
     */
    public function select(PurchaseRequest $purchaseRequest, Offer $offer): RedirectResponse
    {
         // Redirect to the new method flow or just abort
         return redirect()->back()->with('error', 'Please use the selection form with a reason.');
    }

    /**
     * Display the Finance Review page for a request.
     */
    public function financeReview(PurchaseRequest $purchaseRequest): View
    {
        if (!auth()->user()->can('is-finance') && !auth()->user()->can('is-admin')) {
             abort(403);
        }
        
        // Ensure request is in correct status
        if ($purchaseRequest->status !== 'Pending Finance') {
             return redirect()->route('dashboard')->with('error', 'This request is not pending finance approval.');
        }

        $purchaseRequest->load('offers', 'user');
        
        return view('offers.finance_review', compact('purchaseRequest'));
    }

    /**
     * Process Finance's decision (Confirm Cash OR Escalate to Manager).
     */
    public function financeSubmit(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!auth()->user()->can('is-finance') && !auth()->user()->can('is-admin')) {
             abort(403);
        }

        $validated = $request->validate([
            'finance_selected_offer_id' => ['required', 'exists:offers,id'],
            'finance_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $purchaseRequest->load('offers');
        $procurementOffer = $purchaseRequest->offers->where('is_procurement_recommended', true)->first();
        
        // Safety check: if no procurement offer, something is wrong, but we can fallback to chosen
        if (!$procurementOffer) {
            $procurementOffer = $purchaseRequest->offers->where('is_chosen', true)->first();
        }

        $isHighValue = $procurementOffer && $procurementOffer->isHighValue();

        if ($isHighValue) {
            // CASE B: High Value -> Escalate to Manager
            
            // 1. Update Finance Recommendation
            $financeOffer = Offer::find($validated['finance_selected_offer_id']);
            
            // Reset any previous finance choice (if any)
            $purchaseRequest->offers()->update(['is_finance_recommended' => false]);
            
            $financeOffer->is_finance_recommended = true;
            $financeOffer->finance_recommendation_reason = $validated['finance_reason'];
            $financeOffer->save();

            // 2. Update Status
            $oldStatus = $purchaseRequest->status;
            $newStatus = 'Pending Manager Approval';
            $purchaseRequest->status = $newStatus;
            $purchaseRequest->save();

            // Log
            RequestLog::create([
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => 'Finance reviewed. Escalated to Manager for final approval.',
            ]);

            // Notify Manager
            $managers = \App\Models\User::where('role', 'manager')->get();
            \Illuminate\Support\Facades\Notification::send($managers, new \App\Notifications\NewRequestForApprovalNotification($purchaseRequest, 'Final Approval (Manager)'));

            return redirect()->route('dashboard')->with('success', 'Request escalated to Manager.');

        } else {
            // CASE A: Low Value -> Confirm Cash -> Ready to Buy
            
            // 0. Update Selection (Ensure Finance's choice is marked as chosen)
            $financeOffer = Offer::find($validated['finance_selected_offer_id']);
            $purchaseRequest->offers()->update(['is_chosen' => false]);
            $financeOffer->is_chosen = true;
            $financeOffer->save();

            // 1. Update Status
            $oldStatus = $purchaseRequest->status;
            $newStatus = 'Ready to Buy';
            $purchaseRequest->status = $newStatus;
            $purchaseRequest->save();

            // Log
            RequestLog::create([
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => 'Finance confirmed cash availability. Ready to buy.',
            ]);

            // Notify Procurement (Original User + Procurement Team)
            // Actually, "Ready to Buy" is for Procurement to see.
            // We can reuse RequestReadyForPickupNotification or create a generic one.
            // For now, let's notify the Procurement team.
            $procurementUsers = \App\Models\User::whereIn('role', ['procurement', 'admin'])->get();
            \Illuminate\Support\Facades\Notification::send($procurementUsers, new \App\Notifications\NewRequestForApprovalNotification($purchaseRequest, 'Ready to Buy'));

            return redirect()->route('dashboard')->with('success', 'Purchase confirmed. Procurement notified.');
        }
    }

    /**
     * Display the Manager Review page.
     */
    public function managerReview(PurchaseRequest $purchaseRequest): View
    {
        if (!auth()->user()->can('is-manager') && !auth()->user()->can('is-admin')) {
             abort(403);
        }

        if ($purchaseRequest->status !== 'Pending Manager Approval') {
             return redirect()->route('dashboard')->with('error', 'This request is not pending manager approval.');
        }

        $purchaseRequest->load('offers', 'user');

        return view('offers.manager_review', compact('purchaseRequest'));
    }

    /**
     * Process Manager's Final Approval.
     */
    public function managerApprove(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!auth()->user()->can('is-manager') && !auth()->user()->can('is-admin')) {
             abort(403);
        }

        $validated = $request->validate([
            'manager_selected_offer_id' => ['required', 'exists:offers,id'],
            'manager_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $selectedOffer = Offer::findOrFail($validated['manager_selected_offer_id']);

        // 1. Reset selection (Procurement/Finance might have selected something, Manager overrides everything)
        // Actually, we should keep the recommendation flags but update is_chosen.
        $purchaseRequest->offers()->update(['is_chosen' => false]);

        // 2. Set new chosen offer
        $selectedOffer->is_chosen = true;
        // Optionally store manager's reason if we had a column, but we don't have is_manager_recommended column.
        // We can append to log.
        $selectedOffer->save();

        // 3. Update Status
        $oldStatus = $purchaseRequest->status;
        $newStatus = 'Ready to Buy';
        $purchaseRequest->status = $newStatus;
        $purchaseRequest->save();

        // 4. Log
        RequestLog::create([
            'purchase_request_id' => $purchaseRequest->id,
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => 'Manager Final Approval. Selected Vendor: ' . $selectedOffer->vendor_name . '. Reason: ' . ($validated['manager_reason'] ?? 'N/A'),
        ]);

        // 5. Notify Procurement
        $procurementUsers = \App\Models\User::whereIn('role', ['procurement', 'admin'])->get();
        \Illuminate\Support\Facades\Notification::send($procurementUsers, new \App\Notifications\NewRequestForApprovalNotification($purchaseRequest, 'Ready to Buy (Manager Approved)'));

        return redirect()->route('dashboard')->with('success', 'Request approved. Sent to Procurement.');
    }

    /**
     * Display a printable Purchase Order.
     */
    public function printPo(PurchaseRequest $purchaseRequest): View
    {
        if (!auth()->user()->can('is-procurement') && !auth()->user()->can('is-admin')) {
             abort(403);
        }

        $purchaseRequest->load('user', 'chosenOffer');

        if (!$purchaseRequest->chosenOffer) {
             abort(404, 'No chosen offer found for this request.');
        }

        return view('offers.print_po', compact('purchaseRequest'));
    }
}

