<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use App\Models\RequestLog;
use App\Models\User;
use App\Models\Setting;
use App\Notifications\RequestDeniedNotification;
use App\Notifications\NewRequestForApprovalNotification;
use App\Notifications\RequestReadyForPickupNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use App\Services\BudgetService;

class ApprovalController extends Controller
{
    /**
     * Display the approval queue dashboard.
     * This dynamically shows requests based on the user's role.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // 1. Base Query
        $query = PurchaseRequest::with('user');
        
        // 2. Search Filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2.1 Additional Filters
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // 3. Role-Based Filtering
        // $query->where('user_id', '!=', $user->id); // Safety Net REMOVED per user request
        $pageTitle = __('My Approval Queue');

        switch ($user->role) {
            case 'procurement':
                $query->whereIn('status', ['Pending Procurement', 'Fulfilled from Stock']);
                $pageTitle = __('Procurement Queue');
                break;
            case 'finance':
                $query->whereIn('status', ['Pending Finance', 'Pending Final Payment', 'Pending Final Approval']);
                $pageTitle = __('Finance Queue');
                break;
            case 'manager':
                $query->whereIn('status', ['Pending Manager', 'Pending Manager Approval']);
                $pageTitle = __('Manager Queue');
                break;
            case 'admin':
                $query->whereIn('status', [
                    'Pending Procurement', 'Pending Finance', 'Pending Manager', 
                    'Pending Final Payment', 'Pending Final Approval'
                ]);
                $pageTitle = __('All Pending Requests (Admin)');
                break;
            default:
                $query->whereRaw('1 = 0');
                break;
        }

        // 4. Sorting Logic
        $sort = $request->input('sort', 'priority'); 
        $direction = $request->input('direction', 'asc'); 

        // Validate sort column
        if (!in_array($sort, ['item_name', 'estimated_price', 'created_at', 'priority', 'status'])) {
            $sort = 'priority';
        }

        if ($sort === 'priority') {
            // FIELD returns index: high=1, medium=2, low=3. ASC sorts 1->3 (High->Low).
            if ($direction === 'desc') {
                 $query->orderByRaw("FIELD(priority, 'low', 'medium', 'high')");
            } else {
                 $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
            }
        } elseif ($sort === 'estimated_price') {
             $query->orderBy('estimated_price', $direction);
        } else {
             $query->orderBy($sort, $direction);
        }

        // Secondary sort always created_at
        $query->orderBy('created_at', 'asc');

        $requests = $query->paginate(10)->withQueryString();

        return view('approval.queue', compact('requests', 'pageTitle'));
    }

    /**
     * Display the specified request for review.
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        // Make sure the user is authorized to see this.
        // Approvers can view ANY request for historical/audit purposes
        $user = Auth::user();

        // All approvers (procurement, finance, manager, admin) can view any request
        // This allows viewing history for auditing and reference
        $canView = in_array($user->role, ['procurement', 'finance', 'manager', 'admin']);
        
        if (!$canView) {
            return redirect()->route('approval.queue')
                ->with('info', __('This request is not available for your review.'));
        }
        
        // Eager load all history immediately so we can use chosenOffer for budget calc
        $purchaseRequest->load('user', 'requestLogs.user', 'offers', 'chosenOffer');

        $budgetData = null;
        if ($user->role === 'finance' || $user->role === 'admin') {
            $budgetService = new BudgetService();
            // Pass current request ID to exclude it from "Already Spent" calculation
            $budgetData = $budgetService->getBudgetOverview($purchaseRequest->id);

            // Determine Cost and Currency (Priority: Chosen Offer > Estimate)
            $cost = $purchaseRequest->estimated_price;
            $currency = $purchaseRequest->estimated_currency;

            if ($purchaseRequest->chosenOffer) {
                $cost = $purchaseRequest->chosenOffer->price;
                $currency = $purchaseRequest->chosenOffer->currency;
            }

            $budgetData['this_request_cost'] = $cost;
            $budgetData['this_request_currency'] = $currency;

            // Calculate impact on the specific currency budget
            if ($currency === 'USD') {
                $budgetData['remaining_if_approved'] = $budgetData['remaining_usd'] - $cost;
            } else {
                $budgetData['remaining_if_approved'] = $budgetData['remaining_iqd'] - $cost;
            }
        }

        return view('approval.show', compact('purchaseRequest', 'budgetData'));
    }

    /**
     * Process an approval, denial, or escalation.
     */
    public function process(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        $user = Auth::user();
        $action = $request->input('action'); // 'approve', 'deny', 'reject_quote', 'escalate', 'fulfill_stock', 'cash_ready', 'approve_final'
        $comment = $request->input('comment');
        
        // 1. Validate the comment for rejections
        if (($action === 'deny' || $action === 'reject_quote') && empty($comment)) {
            return back()->with('error', __('A comment is required when denying a request or rejection a quotation.'));
        }
        
        $oldStatus = $purchaseRequest->status;
        $newStatus = $oldStatus; // By default, status does not change

        try {
            switch ($user->role) {
                case 'procurement':
                    if ($action === 'fulfill_stock') {
                        $newStatus = 'Fulfilled from Stock';
                        // Notify the user that item is ready
                        $purchaseRequest->user->notify(new RequestReadyForPickupNotification($purchaseRequest));
                    }
                    // Note: Procurement "escalating" is handled by OfferController::select, not here usually.
                    // But if they are here via approval queue for loop-backs:
                    elseif ($action === 'escalate') {
                         // Action: Move to "Needs Quotations" phase
                         $newStatus = 'Approved for Purchase'; 
                         $purchaseRequest->status = $newStatus;
                         $purchaseRequest->save();
                         
                         $this->createLog($purchaseRequest->id, $user->id, $oldStatus, $newStatus, 'Moved to Needs Quotations phase.');
                         
                         // UX Improvement: Redirect immediately to the Offer Management page
                         return redirect()->route('offers.create', $purchaseRequest)
                             ->with('success', __('Request approved for quotations. Please add offers now.'));
                    } elseif ($action === 'approve_final') {
                         $newStatus = 'Purchase Logged';
                         $purchaseRequest->user->notify(new RequestReadyForPickupNotification($purchaseRequest));
                    }
                    break;
                
                case 'finance':
                    if ($action === 'deny') {
                         $newStatus = 'Denied';
                    } elseif ($action === 'reject_quote') {
                         $newStatus = 'Approved for Purchase';
                         $procurementUsers = User::where('role', 'procurement')->get();
                         Notification::send($procurementUsers, new NewRequestForApprovalNotification($purchaseRequest, __('Quote Rejected (Needs New Quotes)')));
                    } elseif ($action === 'finance_approve_high') {
                        // High Value: Escalate to Manager
                        $request->validate(['finance_selected_offer_id' => 'required|exists:offers,id']);
                        
                        $offer = \App\Models\Offer::findOrFail($request->finance_selected_offer_id);
                        $purchaseRequest->offers()->update(['is_finance_recommended' => false]);
                        $offer->is_finance_recommended = true;
                        $offer->finance_recommendation_reason = $request->input('finance_reason');
                        $offer->save();

                        if ($request->input('finance_reason')) {
                            $comment = "Recommendation: " . $request->input('finance_reason');
                        }

                        $newStatus = 'Pending Manager Approval';
                        $managerUsers = User::where('role', 'manager')->get();
                        Notification::send($managerUsers, new NewRequestForApprovalNotification($purchaseRequest, __('Manager Approval')));

                    } elseif ($action === 'finance_approve_low') {
                        // Low Value: Confirm Cash -> Ready to Buy

                        // Security Check: Ensure there is at least one offer recommended or chosen
                        $hasOffer = \App\Models\Offer::where('purchase_request_id', $purchaseRequest->id)
                            ->where(function($q) {
                                $q->where('is_procurement_recommended', true)
                                  ->orWhere('is_chosen', true);
                            })->exists();

                        // Fallback: If no specific recommendation, just ensure ANY offer exists if we are approving based on "Low Value"
                        if (!$hasOffer) {
                            $hasOffer = $purchaseRequest->offers()->exists();
                        }

                        if (!$hasOffer) {
                            return back()->with('error', __('Cannot approve. No offers have been selected or recommended.'));
                        }

                        $newStatus = 'Ready to Buy';
                        $procurementUsers = User::where('role', 'procurement')->get();
                        Notification::send($procurementUsers, new NewRequestForApprovalNotification($purchaseRequest, __('Cash Ready - Ready to Buy')));

                    } elseif ($action === 'approve') {
                        // Default fallthrough or legacy support
                    } elseif ($action === 'cash_ready') {
                        // Old flow for "Pending Final Payment"
                        
                        // SAFETY: Ensure an offer is chosen before moving to Ready to Buy
                        $hasChosen = $purchaseRequest->offers()->where('is_chosen', true)->exists();
                        if (!$hasChosen) {
                            $bestOffer = $purchaseRequest->offers()
                                ->orderBy('is_finance_recommended', 'desc')
                                ->orderBy('is_procurement_recommended', 'desc')
                                ->orderBy('price', 'asc')
                                ->first();

                            if ($bestOffer) {
                                $bestOffer->is_chosen = true;
                                $bestOffer->save();
                            }
                        }

                        $newStatus = 'Ready to Buy';
                        $procurementUsers = User::where('role', 'procurement')->get();
                        Notification::send($procurementUsers, new NewRequestForApprovalNotification($purchaseRequest, 'Cash Ready - Ready to Buy'));
                    }
                    break;
                
                case 'manager':
                    if ($action === 'deny') {
                        $newStatus = 'Denied';
                    } elseif ($action === 'reject_quote') {
                        $newStatus = 'Approved for Purchase';
                        $procurementUsers = User::where('role', 'procurement')->get();
                        Notification::send($procurementUsers, new NewRequestForApprovalNotification($purchaseRequest, __('Quote Rejected by Manager')));
                    } elseif ($action === 'manager_approve') {
                        // Manager Final Selection
                        $request->validate(['manager_selected_offer_id' => 'required|exists:offers,id']);
                        
                        $offer = \App\Models\Offer::findOrFail($request->manager_selected_offer_id);
                        
                        $purchaseRequest->offers()->update(['is_chosen' => false]);
                        $offer->is_chosen = true;
                        $offer->save();

                        $reason = $request->input('manager_reason');
                        if ($reason) {
                            $comment = "Manager Note: " . $reason;
                        }

                        $newStatus = 'Pending Final Payment';
                        $financeUsers = User::where('role', 'finance')->get();
                        Notification::send($financeUsers, new NewRequestForApprovalNotification($purchaseRequest, __('Manager Approved - Pending Cash Confirmation')));

                    } elseif ($action === 'approve') {
                         // Legacy support
                         $newStatus = 'Ready to Buy';
                    }
                    break;
                
                case 'admin':
                    // Admin overrides
                    if ($action === 'approve') {
                         // Intelligent Admin Approve: Ensure an offer is selected
                         $hasChosen = $purchaseRequest->offers()->where('is_chosen', true)->exists();
                         
                         if (!$hasChosen) {
                             // Auto-select the best available offer
                             $bestOffer = $purchaseRequest->offers()
                                         ->orderBy('is_finance_recommended', 'desc')
                                         ->orderBy('is_procurement_recommended', 'desc')
                                         ->orderBy('price', 'asc')
                                         ->first();
                             
                             if ($bestOffer) {
                                  $bestOffer->is_chosen = true;
                                  $bestOffer->save();
                             } else {
                                  return back()->with('error', 'Cannot approve: No offers exist for this request.');
                             }
                         }

                         // Treat as fully approved -> Pending Final Payment (Cash Ready check)
                         $newStatus = 'Pending Final Payment';
                    }
                    if ($action === 'reject_quote') $newStatus = 'Approved for Purchase';
                    if ($action === 'deny') $newStatus = 'Denied';
                    if ($action === 'approve_final') {
                         $newStatus = 'Purchase Logged';
                         $purchaseRequest->user->notify(new RequestReadyForPickupNotification($purchaseRequest));
                    }
                    break;
            }

            // 4. Update database only if status changed
            if ($oldStatus !== $newStatus) {
                $purchaseRequest->status = $newStatus;
                $purchaseRequest->save();

                // 5. Create the log entry
                $this->createLog($purchaseRequest->id, $user->id, $oldStatus, $newStatus, $comment);

                // 6. Send Notifications for Denial
                if ($newStatus === 'Denied') {
                    $purchaseRequest->user->notify(new RequestDeniedNotification($purchaseRequest, $comment ?? __('No reason provided.')));
                }
            }
            
            return redirect()->route('approval.queue')->with('success', __('Request processed successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to create a request log.
     */
    private function createLog(int $requestId, int $userId, string $oldStatus, string $newStatus, ?string $comment): void
    {
        RequestLog::create([
            'purchase_request_id' => $requestId,
            'user_id' => $userId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $comment,
        ]);
    }
}
