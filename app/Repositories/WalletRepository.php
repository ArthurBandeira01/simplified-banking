<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Wallet;

class WalletRepository
{
    public $wallet;

    public function __construct(Wallet $wallet) {
        $this->wallet = $wallet;
    }

    /**
     * Find a wallet by its ID.
     *
     * @param int $walletId
     * @return Wallet|null
     */
    public function findWalletById($walletId) {
        return $this->wallet->find($walletId);
    }
}
