<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read and redirect.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $notification = Auth::user()->unreadNotifications()->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Check if this is a purchase request notification
            $requestId = $notification->data['request_id'] ?? $notification->data['purchase_request_id'] ?? null;

            if ($requestId) {
                $purchaseRequest = \App\Models\PurchaseRequest::find($requestId);
                
                // Validate that the purchase request exists and user can access it
                if ($purchaseRequest) {
                    $user = Auth::user();
                    
                    // Check if user can view this request
                    if ($purchaseRequest->user_id === $user->id || 
                        $user->can('is-admin') || 
                        $user->can('is-finance') || 
                        $user->can('is-manager') ||
                        $user->can('is-procurement')) {
                        
                        // Redirect to appropriate view based on user role
                        if ($purchaseRequest->user_id === $user->id) {
                            return redirect()->route('requests.show', $requestId);
                        } else {
                            // If they are an approver, send them to the approval page
                            // But if they are just viewing their own request, send to requests.show
                            return redirect()->route('approval.show', $requestId);
                        }
                    }
                }
            }
            
            // Try to redirect to the link in the notification data if no request_id
            if (isset($notification->data['url'])) {
                return redirect($notification->data['url']);
            }
        }
        
        // Fallback to dashboard if notification not found, no valid URL, or request doesn't exist
        return redirect()->route('dashboard')->with('info', 'The notification you clicked is no longer available.');
    }
}
