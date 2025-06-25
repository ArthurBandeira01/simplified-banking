<?php

namespace App\Services;

use App\Models\TransactionHistory;
use App\Repositories\TransactionHistoryRepository;

class TransactionHistoryService
{
    public $transactionHistoryRepository;

    public function __construct(TransactionHistoryRepository $transactionHistoryRepository) {
        $this->transactionHistoryRepository = $transactionHistoryRepository;
    }

    /**
     * Create a transaction history.
     */
    public function create(int $transactionId, string $status, string $description): TransactionHistory
    {
        return $this->transactionHistoryRepository->create($transactionId, $status, $description);
    }
}
