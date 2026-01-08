<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\PurchaseRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for MySQL "Specified key was too long" error
        Schema::defaultStringLength(191);

        // --- Rate Limiting Configuration ---
        // Global rate limiter for all web requests
        \Illuminate\Support\Facades\RateLimiter::for('global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (\Illuminate\Http\Request $request, array $headers) {
                    return response()->view('errors.429', [], 429)->withHeaders($headers);
                });
        });

        // Stricter rate limiter for authentication attempts
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (\Illuminate\Http\Request $request, array $headers) {
                    return response()->view('errors.429', [], 429)->withHeaders($headers);
                });
        });

        // Force Carbon to use Sorani (ckb) when the locale is set to 'ku' (which defaults to Kurmanji)
        // because the user prefers Sorani for the Kurdish option.
        if (app()->getLocale() === 'ku') {
             \Carbon\Carbon::setLocale('ckb');
        }

        View::composer('*', function ($view) {
            // Share site settings with all views
            try {
                $siteSettings = \App\Models\Setting::getAllCached();
            } catch (\Exception $e) {
                $siteSettings = [];
            }
            $view->with('siteSettings', $siteSettings);

            if (Auth::check()) {
                $user = Auth::user();
                $counts = [
                    'approval' => 0,
                    'ready_to_buy' => 0,
                ];
                
                // Get all unread notifications and filter out stale ones
                $allUnreadNotifications = $user->unreadNotifications()->get();
                $unreadNotifications = $allUnreadNotifications->filter(function ($notification) {
                    // If it's not a purchase request notification, keep it
                    if (!str_contains($notification->type, 'PurchaseRequest')) {
                        return true;
                    }
                    
                    // Check if the related purchase request is still actionable
                    if (isset($notification->data['request_id'])) {
                        $purchaseRequest = \App\Models\PurchaseRequest::find($notification->data['request_id']);
                        // Only keep notifications for requests that are still pending (not completed/denied)
                        return $purchaseRequest && !in_array($purchaseRequest->status, [
                            'Completed', 'Denied', 'Purchase Logged', 'Fulfilled from Stock'
                        ]);
                    }
                    
                    return true; // Keep notifications without request_id
                });
                
                $unreadNotificationCount = $unreadNotifications->count();

                if ($user->can('is-approver')) {
                    $query = PurchaseRequest::where('user_id', '!=', $user->id);
                    if ($user->role === 'procurement') {
                        $query->whereIn('status', ['Pending Procurement', 'Ready to Buy']);
                    } elseif ($user->role === 'finance') {
                        $query->whereIn('status', ['Pending Finance', 'Pending Final Payment', 'Pending Final Approval']);
                    } elseif ($user->role === 'manager') {
                        $query->whereIn('status', ['Pending Manager', 'Pending Manager Approval']);
                    } elseif ($user->role === 'admin') {
                        $query->whereIn('status', ['Pending Procurement', 'Pending Finance', 'Pending Manager', 
                                                   'Pending Manager Approval', 'Pending Final Payment', 'Pending Final Approval']);
                    }
                    $counts['approval'] = $query->count();
                }
                
                if ($user->can('is-procurement') || $user->can('is-admin')) {
                    // Count for "Needs Quotations" (items waiting for offers)
                    $counts['needs_quotations'] = PurchaseRequest::where('status', 'Approved for Purchase')->count();
                    // Count for "Ready to Buy (Cash)" (items approved and cash is ready)
                    $counts['ready_to_buy'] = PurchaseRequest::where('status', 'Ready to Buy')->count();
                }
                
                $view->with('queueCounts', $counts)
                     ->with('unreadNotifications', $unreadNotifications)
                     ->with('unreadNotificationCount', $unreadNotificationCount);
            }
        });
    }
}
