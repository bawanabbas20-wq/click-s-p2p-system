<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;



class PurchaseRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'parent_request_id',
        'item_name',
        'estimated_price',
        'estimated_currency',
        'priority',
        'date_wanted',
        'justification',
        'status',
    ];

    /**
     * Get the user (employee) who created this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all the history/comment logs for this request.
     */
    public function requestLogs(): HasMany
    {
        return $this->hasMany(RequestLog::class);
    }

    /**
     * Get all the offers (quotations) for this request.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Get the one offer that was chosen.
     */
    public function chosenOffer(): HasOne
    {
        return $this->hasOne(Offer::class)->where('is_chosen', true);
    }

    /**
     * Get the parent request (if this is a resubmitted request).
     */
    public function parentRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'parent_request_id');
    }

    /**
     * Get all child requests (resubmissions of this request).
     */
    public function childRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class, 'parent_request_id');
    }
}

