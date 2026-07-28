<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PurchaseRequest;
use App\Models\Offer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find all completed requests that don't have any offers
        $completedRequests = PurchaseRequest::whereIn('status', ['Completed', 'Purchase Logged'])
            ->whereDoesntHave('offers')
            ->get();

        foreach ($completedRequests as $request) {
            // Create a chosen offer based on the request's estimated price
            // This is a reasonable assumption since these were completed requests
            Offer::create([
                'purchase_request_id' => $request->id,
                'vendor_name' => 'Historical Purchase', // Generic vendor name for old data
                'price' => $request->estimated_price,
                'currency' => $request->estimated_currency,
                'quotation_file_path' => null,
                'is_chosen' => true, // Mark as chosen since these were completed
                'created_at' => $request->updated_at, // Use the request's completion date
                'updated_at' => $request->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the historical offers we created
        Offer::where('vendor_name', 'Historical Purchase')->delete();
    }
};
