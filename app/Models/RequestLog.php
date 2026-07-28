<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'user_id',
        'old_status',
        'new_status',
        'comment',
    ];

    /**
     * Get the main request this log belongs to.
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the user who wrote this log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

