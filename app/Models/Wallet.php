<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'customer_id',
        'retailer_id',
        'balance'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function owner(): BelongsTo
    {
        return $this->customer ?? $this->retailer;
    }

    public function isRetailer(): bool
    {
        return !is_null($this->retailer_id);
    }
}
