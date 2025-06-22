<?php

namespace App\Services;

use App\Repositories\WalletRepository;
use Illuminate\Http\Request;

class WalletService
{
    public $walletRepository;

    public function __construct(WalletRepository $walletRepository) {
        $this->walletRepository = $walletRepository;
    }

    /**
     * Get wallet by ID.
     */
    public function findWalletById(int $walletId) {
        return $this->walletRepository->findWalletById($walletId);
    }
}
