<?php

namespace App\Repositories;

use App\Models\TransactionHistory;

class TransactionHistoryRepository
{
    public $transactionHistory;

    public function __construct(TransactionHistory $transactionHistory) {
        $this->transactionHistory = $transactionHistory;
    }

    /**
     * Create a transaction history
     */
    public function create(int $transactionId, string $status, $description): TransactionHistory
    {
        return $this->transactionHistory->create([
            'transaction_id' => $transactionId,
            'status' => $status,
            'description' => $description
        ]);
    }
}
