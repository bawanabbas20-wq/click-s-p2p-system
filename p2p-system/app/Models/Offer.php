<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'vendor_id', // Added
        'vendor_name',
        'price',
        'currency',
        'quotation_file_path',
        'is_chosen',
        'is_procurement_recommended',
        'procurement_recommendation_reason',
        'is_finance_recommended',
        'finance_recommendation_reason',
    ];

    protected $casts = [
        'is_chosen' => 'boolean',
        'is_procurement_recommended' => 'boolean',
        'is_finance_recommended' => 'boolean',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Check if the offer is considered "High Value" (>= 100,000 IQD).
     *
     * @return bool
     */
    public function isHighValue(): bool
    {
        $thresholdIqd = 100000;
        
        if ($this->currency === 'IQD') {
            return $this->price >= $thresholdIqd;
        }
        
        // If USD, convert to IQD
        $exchangeRate = \App\Models\Setting::where('key', 'exchange_rate_usd_to_iqd')->value('value') ?? 1450;
        $priceInIqd = $this->price * $exchangeRate;
        
        return $priceInIqd >= $thresholdIqd;
    }
}
