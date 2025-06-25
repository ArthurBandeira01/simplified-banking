<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;

class TransactionService
{
    public $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository) {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Create transaction.
     */
    public function create(array $data): Transaction
    {
        return $this->transactionRepository->create($data);
    }
}
