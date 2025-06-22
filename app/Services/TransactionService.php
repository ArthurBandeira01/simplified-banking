<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class TransactionService
{
    public $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository) {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Create transaction.
     */
    public function create(array $data)
    {
        $this->transactionRepository->create($data);
    }
}
