<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'rating',
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
