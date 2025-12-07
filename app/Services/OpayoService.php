<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class OpayoService
{
    protected string $endpoint;
    protected string $integrationKey;
    protected string $integrationPassword;
    protected string $vendorName;

    public function __construct()
    {
        // Load configuration - FIXED: use 'endpoint' not 'base_url'
        $this->endpoint = rtrim(config('services.opayo.endpoint', 'https://sandbox.opayo.eu.elavon.com'), '/');
        $this->integrationKey = config('services.opayo.integration_key');
        $this->integrationPassword = config('services.opayo.integration_password');
        $this->vendorName = config('services.opayo.vendor_name');

        // Validate credentials
        if (empty($this->integrationKey) || empty($this->integrationPassword)) {
            throw new \RuntimeException('Opayo credentials not configured. Check your .env file.');
        }
    }

    /**
     * Get full API URL
     */
    private function getApiUrl(string $path): string
    {
        return $this->endpoint . '/api/v1' . $path;
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
     */
    public function createMerchantSessionKey(?string $vendorName = null): Response
    {
        $vendor = $vendorName ?? $this->vendorName;

        if (empty($vendor)) {
            throw new \InvalidArgumentException('Vendor name is required');
        }

        $url = $this->getApiUrl('/merchant-session-keys');

        Log::info('Opayo: Creating MSK', ['vendor' => $vendor, 'url' => $url]);

        $response = $this->client()->post($url, [
            'vendorName' => $vendor
        ]);

        if ($response->successful()) {
            Log::info('Opayo: MSK created successfully');
        } else {
            Log::error('Opayo: MSK creation failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Create Card Identifier
     */
    public function createCardIdentifier(string $merchantSessionKey, array $cardDetails): Response
    {
        $url = $this->getApiUrl('/card-identifiers');

        Log::info('Opayo: Creating Card Identifier');

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
            Log::info('Opayo: Card Identifier created');
        } else {
            Log::error('Opayo: Card Identifier failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response;
    }

    /**
     * Create Transaction
     */
    public function createTransaction(array $payload): Response
    {
        $url = $this->getApiUrl('/transactions');

        Log::info('Opayo: Creating transaction', [
            'url' => $url,
            'transactionType' => $payload['transactionType'] ?? 'unknown',
            'vendorTxCode' => $payload['vendorTxCode'] ?? 'unknown'
        ]);

        $response = $this->client()->post($url, $payload);

        if ($response->successful()) {
            Log::info('Opayo: Transaction created', [
                'transactionId' => $response->json('transactionId')
            ]);
        } else {
            $body = [];
            try {
                $body = $response->json();
            } catch (\Exception $e) {
                $body = ['raw' => $response->body()];
            }

            Log::error('Opayo: Transaction failed', [
                'status' => $response->status(),
                'body' => $body
            ]);
        }

        return $response;
    }

    /**
     * Retrieve a transaction
     */
    public function retrieveTransaction(string $transactionId): Response
    {
        $url = $this->getApiUrl("/transactions/{$transactionId}");

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
     * Submit 3D Secure Challenge
     */
    public function submit3DSecureChallenge(string $transactionId, string $cRes): Response
    {
        $url = $this->getApiUrl("/transactions/{$transactionId}/3d-secure-challenge");
        
        Log::info('Opayo: Submitting 3DS challenge', [
            'url' => $url,
            'transactionId' => $transactionId
        ]);

        $response = $this->client()->post($url, [
            'cRes' => $cRes
        ]);

        if (!$response->successful()) {
            Log::error('Opayo: 3DS challenge failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }

        return $response;
    }

    /**
     * Create an instruction (void, release, abort, cancel)
     */
    public function createInstruction(string $transactionId, string $instructionType, ?int $amount = null): Response
    {
        $url = $this->getApiUrl("/transactions/{$transactionId}/instructions");

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