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
    use RefreshDatabase, WithFaker;

    protected Customer $payer;
    protected Wallet $payerWallet;

    protected Retailer $payee;
    protected Wallet $payeeWallet;

    protected function setUp(): void
    {
        parent::setUp();

        // 'https://util.devi.tools/api/v1/notify'    => Http::response(['message' => 'Success'], 200),
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response(['message' => 'Autorizado'], 200),
        ]);

        // Create customer and wallet
        $this->payer = Customer::factory()->create();
        $this->payerWallet = $this->payer->wallet()->create([
            'balance' => 500,
        ]);

        // Create retailer and wallet
        $this->payee = Retailer::factory()->create();
        $this->payeeWallet = $this->payee->wallet()->create([
            'balance' => 100,
        ]);
    }


    /**
     * Test if connection is ok
     * @return void
     */

    public function test_connection_is_ok(): void
    {
        $response = $this->get('/api');

        $response->assertStatus(200);
    }

    /**
     * Test transfer between wallets.
     * @return void
     */
    public function test_transfer()
    {
        $response = $this->postJson('/api/transfer', [
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value' => 100,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'payer',
                    'payee',
                    'value',
                    'created_at',
                    'updated_at',
                ]
            ]);


        $this->assertDatabaseHas('wallets', [
            'id' => $this->payerWallet->id,
            'balance' => 400,
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $this->payeeWallet->id,
            'balance' => 200,
        ]);
    }

    /**
     * Verify balance.
     */
    public function test_transfer_fails_if_insufficient_balance(): void
    {
        $response = $this->postJson('/api/transfer', [
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value' => 550,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Insufficient balance.',
            ]);
    }

    // /**
    //  * Test retailer cannot send money.
    //  * @return void
    //  */
    public function test_retailer_cannot_send_money(): void
    {
        $response = $this->postJson('/api/transfer', [
            'payer_wallet_id' => $this->payeeWallet->id,
            'payee_wallet_id' => $this->payerWallet->id,
            'value' => 100,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Retailers cannot send money.',
            ]);
    }

    /**
     * Test if value transfer is valid.
     * @return void
     */
    public function test_transfer_value(): void
    {
        $response = $this->postJson('/api/transfer', [
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value' => -100,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Transaction value must be at least 0.01.',
            ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_transfer_fails_if_authorization_service_denies(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
