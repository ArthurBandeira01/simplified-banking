<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\CustomerService;
use App\Services\RetailerService;
use App\Services\TransactionHistoryService;
use App\Services\TransactionService;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransactionController
{
    public $customerService;
    public $retailerService;
    public $transactionService;
    public $transactionHistoryService;
    public $walletService;

    public function __construct(
        CustomerService $customerService,
        RetailerService $retailerService,
        TransactionService $transactionService,
        TransactionHistoryService $transactionHistoryService,
        WalletService $walletService

    ) {
        $this->customerService = $customerService;
        $this->retailerService = $retailerService;
        $this->transactionService = $transactionService;
        $this->transactionHistoryService = $transactionHistoryService;
        $this->walletService = $walletService;
    }

    /**
     * Verify the status of the API.
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $databaseConnection = DB::connection()->getPdo() ? 'Connected' : 'Not Connected';
        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        $statusCode = $databaseConnection === 'Not Connected' ? $statusCode = 503 : 200;

        return response()->json([
            'Status' => $statusCode,
            'DatabaseConnection' => $databaseConnection,
            'MemoryUsage' => $memoryUsage . ' MB',
        ], $statusCode);
    }

    /**
     * Make a transaction.
     */
    public function transfer(TransactionRequest $request)
    {
        $data = $request->all();
        $payerWallet = $this->walletService->findWalletById($data['payer_wallet_id']);
        $payeeWallet = $this->walletService->findWalletById($data['payee_wallet_id']);

        if ($payerWallet->isRetailer()) {
            return response()->json(['error' => 'Retailers cannot send money.'], 403);
        }

        if ($payerWallet->balance < $data['value']) {
            return response()->json(['error' => 'Insufficient balance.'], 400);
        }

        // Verify authorization
       $authResponse = Http::get('https://util.devi.tools/api/v2/authorize');

        if (
            $authResponse->failed() ||
            $authResponse->json('status') !== 'success' ||
            $authResponse->json('data.authorization') !== true
        ) {
            return response()->json(['error' => 'Transfer not authorized.'], 403);
        }

        DB::beginTransaction();

        try {
            $transaction = $this->transactionService->create($data);
            $description = 'Transaction completed.';

            $payerWallet->decrement('balance', $data['value']);
            $payeeWallet->increment('balance', $data['value']);

            $this->transactionHistoryService->create($transaction->id, 'completed', $description);

            DB::commit();

            // Notificar recebedor
            Http::post('https://util.devi.tools/api/v1/notify', [
                'message' => 'You have received a transfer of R$ ' . number_format($data['value'], 2, ',', '.')
            ]);

            return new TransactionResource($transaction);

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($transaction)) {
                $description = 'Transaction failed: ' . $e;
                $this->transactionHistoryService->create($transaction->id, 500, $description);
            }

            return response()->json([
                'error' => 'Transaction failed',
                'details' => $e->getMessage()
            ], 500);

        }
    }
}
