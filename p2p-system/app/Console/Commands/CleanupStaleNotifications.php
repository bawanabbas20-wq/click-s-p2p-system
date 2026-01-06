<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequest;

class CleanupStaleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:cleanup-stale';

    /**
     * The description of the console command.
     */
    protected $description = 'Mark notifications as read for completed/denied purchase requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of stale notifications...');
        
        // Get all unread notifications that are purchase request related
        $staleNotifications = DB::table('notifications')
            ->whereNull('read_at')
            ->where('type', 'LIKE', '%PurchaseRequest%')
            ->get();
        
        $cleanedCount = 0;
        
        foreach ($staleNotifications as $notification) {
            $data = json_decode($notification->data, true);
            
            if (isset($data['request_id'])) {
                $purchaseRequest = PurchaseRequest::find($data['request_id']);
                
                // If request doesn't exist or is in a final state, mark notification as read
                if (!$purchaseRequest || in_array($purchaseRequest->status, [
                    'Completed', 'Denied', 'Purchase Logged', 'Fulfilled from Stock'
                ])) {
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update(['read_at' => now()]);
                    
                    $cleanedCount++;
                }
            }
        }
        
        $this->info("Cleaned up {$cleanedCount} stale notifications.");
        
        return 0;
    }
}
