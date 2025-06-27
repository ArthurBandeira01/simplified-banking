<?php

namespace Tests\Feature\Api\Transaction;

use App\Models\Customer;
use App\Models\Retailer;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Helpers\FakeHttpTestResponse;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Customer $payer;
    protected Wallet $payerWallet;

    protected Retailer $payee;
    protected Wallet $payeeWallet;

    protected array $authorizedResponse;

    protected array $unauthorizedResponse;

    protected $authorizeUrl = 'https://util.devi.tools/api/v2/authorize';

    protected $notifyUrl = "https://util.devi.tools/api/v1/notify";

    protected $transferUri = 'api/transfer';

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedResponse = [
            'status' => 'success',
            'data' => ['authorization' => true],
        ];

        $this->unauthorizedResponse = [
            'status' => 'fail',
            'data' => ['authorization' => false],
        ];

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
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);


        $response = $this->postJson($this->transferUri, [
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
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);

        $response = $this->postJson($this->transferUri, [
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
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);

        $response = $this->postJson($this->transferUri, [
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
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);

        $response = $this->postJson($this->transferUri, [
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
     * Test if isn't same wallet.
     * @return void
     */
    public function test_transfer_different_wallets(): void
    {
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);

        $response = $this->postJson($this->transferUri, [
            'payer_wallet_id' => $this->payeeWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value' => 100,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Payer and payee wallets cannot be the same.',
            ]);
    }

    /**
     * Test if there's value.
     * @return void
     */
    public function test_transfer_have_value(): void
    {
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);

        $response = $this->postJson($this->transferUri, [
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Transaction value is required.',
            ]);
    }

    /**
     * Test if value is numeric.
     * @return void
     */
    public function test_transfer_if_value_is_numeric(): void
    {
        Http::fake([
            $this->authorizeUrl => Http::response($this->authorizedResponse, 200),
        ]);

        $response = $this->postJson($this->transferUri, [
            'payer_wallet_id' => $this->payerWallet->id,
            'payee_wallet_id' => $this->payeeWallet->id,
            'value' => 'not-a-number',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Transaction value must be a number.',
            ]);
    }

    /**
     * Summary of test_transfer_authorization
     * @return void
     */
    public function test_transfer_authorization()
    {
        $response = Http::get($this->authorizeUrl);
        $wrapped = FakeHttpTestResponse::fromHttpClient($response);

        if ($wrapped->status() === 200) {
            $wrapped->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'data' => ['authorization' => true],
                ]);
        } else {
            $wrapped->assertStatus(403)
                ->assertJson([
                    'status' => 'fail',
                    'data' => ['authorization' => false],
                ]);
        }
    }

    /**
     * Summary of test_transfer_notify
     * @return void
     */
    public function test_transfer_notify()
    {
        Http::fake([
            $this->notifyUrl => Http::response(null, 204),
        ]);

        $response = Http::post($this->notifyUrl, [
            'message' => 'You have received a transfer of R$ 100,00'
        ]);

        $this->assertEquals(204, $response->status());
        $this->assertTrue($response->successful()); // 2xx
    }
}
