<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'payer_wallet_id',
        'payee_wallet_id',
        'value'
    ];

    public function payerWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'payer_wallet_id');
    }

    public function payeeWallet()
    {
        return $this->belongsTo(Wallet::class, 'payee_wallet_id');
    }
}
