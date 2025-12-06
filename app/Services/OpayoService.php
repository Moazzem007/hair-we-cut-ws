<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class OpayoService
{
    protected string $baseUrl;
    protected string $integrationKey;
    protected string $integrationPassword;
    protected string $vendorName;

    public function __construct()
    {
        // Load configuration
        $this->baseUrl = config('services.opayo.base_url', 'https://sandbox.opayo.eu.elavon.com/api/v1');
        $this->integrationKey = config('services.opayo.integration_key');
        $this->integrationPassword = config('services.opayo.integration_password');
        $this->vendorName = config('services.opayo.vendor_name');

        // Validate credentials
        if (empty($this->integrationKey) || empty($this->integrationPassword)) {
            throw new \RuntimeException('Opayo credentials not configured. Check your .env file.');
        }
    }

    /**
     * Create HTTP client with proper authentication
     */
    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth($this->integrationKey, $this->integrationPassword)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->retry(2, 100);
    }

    /**
     * Create Merchant Session Key
     *
     * @param string|null $vendorName Override default vendor name
     * @return Response
     */
    public function createMerchantSessionKey(?string $vendorName = null): Response
    {
        $vendor = $vendorName ?? $this->vendorName;

        if (empty($vendor)) {
            throw new \InvalidArgumentException('Vendor name is required');
        }

        $url = $this->baseUrl . '/merchant-session-keys';

        Log::info('Opayo: Creating Merchant Session Key', ['vendor' => $vendor]);

        $response = $this->client()->post($url, [
            'vendorName' => $vendor
        ]);

        if ($response->successful()) {
            Log::info('Opayo: Merchant Session Key created successfully');
        } else {
            Log::error('Opayo: Failed to create Merchant Session Key', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Create Card Identifier
     *
     * @param string $merchantSessionKey
     * @param array $cardDetails
     * @return Response
     */
    public function createCardIdentifier(string $merchantSessionKey, array $cardDetails): Response
    {
        $url = $this->baseUrl . '/card-identifiers';

        Log::info('Opayo: Creating Card Identifier');

        // Use merchant session key as Bearer token for card identifier creation
        $response = Http::withToken($merchantSessionKey)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post($url, [
                'cardDetails' => $cardDetails
            ]);

        if ($response->successful()) {
            Log::info('Opayo: Card Identifier created successfully');
        } else {
            Log::error('Opayo: Failed to create Card Identifier', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Create Transaction (Payment / Deferred / Refund / etc)
     *
     * @param array $payload
     * @return Response
     */
    public function createTransaction(array $payload): Response
    {
        $url = $this->baseUrl . '/transactions';

        Log::info('Opayo: Creating transaction', [
            'transactionType' => $payload['transactionType'] ?? 'unknown'
        ]);

        $response = $this->client()->post($url, $payload);

        if ($response->successful()) {
            Log::info('Opayo: Transaction created successfully', [
                'transactionId' => $response->json('transactionId')
            ]);
        } else {
            Log::error('Opayo: Transaction failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Retrieve a transaction
     *
     * @param string $transactionId
     * @return Response
     */
    public function retrieveTransaction(string $transactionId): Response
    {
        $url = $this->baseUrl . '/transactions/' . $transactionId;

        Log::info('Opayo: Retrieving transaction', ['transactionId' => $transactionId]);

        $response = $this->client()->get($url);

        if (!$response->successful()) {
            Log::error('Opayo: Failed to retrieve transaction', [
                'transactionId' => $transactionId,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Handle 3D Secure Challenge
     *
     * @param string $transactionId
     * @param string $cRes
     * @return Response
     */
    public function submit3DSecureChallenge(string $transactionId, string $cRes): Response
    {
        $url = $this->baseUrl . '/transactions/' . $transactionId . '/3d-secure-challenge';

        Log::info('Opayo: Submitting 3D Secure challenge', ['transactionId' => $transactionId]);

        $response = $this->client()->post($url, [
            'cRes' => $cRes
        ]);

        if (!$response->successful()) {
            Log::error('Opayo: 3D Secure challenge failed', [
                'transactionId' => $transactionId,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Create an instruction (void, release, abort, cancel)
     *
     * @param string $transactionId
     * @param string $instructionType
     * @param int|null $amount For release instructions
     * @return Response
     */
    public function createInstruction(string $transactionId, string $instructionType, ?int $amount = null): Response
    {
        $url = $this->baseUrl . '/transactions/' . $transactionId . '/instructions';

        $payload = ['instructionType' => $instructionType];

        if ($instructionType === 'release' && $amount !== null) {
            $payload['amount'] = $amount;
        }

        Log::info('Opayo: Creating instruction', [
            'transactionId' => $transactionId,
            'instructionType' => $instructionType
        ]);

        $response = $this->client()->post($url, $payload);

        if (!$response->successful()) {
            Log::error('Opayo: Instruction failed', [
                'transactionId' => $transactionId,
                'instructionType' => $instructionType,
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }
}
