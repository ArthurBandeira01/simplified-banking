<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    public $transaction;

    public function __construct(Transaction $transaction) {
        $this->transaction = $transaction;
    }

    /**
     * Create a transaction.
     */
    public function create(array $data): Transaction
    {
        return $this->transaction->create([
            'payer_wallet_id' => $data['payer_wallet_id'],
            'payee_wallet_id' => $data['payee_wallet_id'],
            'value' => $data['value'],
        ]);
    }
}
