<?php

namespace Tests\Feature\Api\Transaction;

use App\Models\Customer;
use App\Models\Retailer;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    protected Customer $payer;
    protected Wallet $payerWallet;

    protected Retailer $payee;
    protected Wallet $payeeWallet;

    protected function setUp(): void
    {
        parent::setUp();

        // Mocks das APIs externas
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200),
            'https://util.devi.tools/api/v1/notify'    => Http::response(['message' => 'Success'], 200),
        ]);

        // Cria customer e carteira
        $this->payer = Customer::factory()->create();
        $this->payerWallet = $this->payer->wallet()->create([
            'balance' => 500,
        ]);

        // Cria retailer e carteira
        $this->payee = Retailer::factory()->create();
        $this->payeeWallet = $this->payee->wallet()->create([
            'balance' => 100,
        ]);
    }

    public function test_customer_can_transfer_to_retailer()
    {
        $response = $this->postJson('/api/transfer', [
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value' => 150,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Transaction completed successfully',
                 ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $this->payerWallet->id,
            'balance' => 350,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $this->payeeWallet->id,
            'balance' => 250,
        ]);
    }

    /**
     * A basic feature test example.
     */
    // public function test_transfer_fails_if_insufficient_balance(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // /**
    //  * A basic feature test example.
    //  */
    // public function test_retailer_cannot_send_money(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // /**
    //  * A basic feature test example.
    //  */
    // public function test_transfer_fails_if_authorization_service_denies(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
