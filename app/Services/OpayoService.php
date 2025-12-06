<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpayoService
{
    protected $base;
    protected $key;
    protected $pass;

    public function __construct()
    {
        $this->base = env('OPAYO_BASE') ?? 'https://sandbox.opayo.eu.elavon.com/api/v1';
        $this->key = env('OPAYO_INTEGRATION_KEY') ?? 'fxa0IVHpPZQJmwXv1junS4onhQUzCBAmbsQgwBiTpif9K9O3U8';
        $this->pass = env('OPAYO_INTEGRATION_PASSWORD') ?? '99qEGYYMxDZJXlKzWwY8tW2LafsMxY0pLI1CtEsXsaXAb6RpCgJQ7uWzjwfPwcHxR';
        // $this->base = env('OPAYO_BASE');
        // $this->key = env('OPAYO_INTEGRATION_KEY');
        // $this->pass = env('OPAYO_INTEGRATION_PASSWORD');
    }

    protected function basicClient()
    {
        return Http::withBasicAuth($this->key, $this->pass)
                   ->withOptions(['verify' => filter_var(env('Guzzle_VERIFY', true), FILTER_VALIDATE_BOOLEAN)])
                   ->acceptJson();
    }

    // Create Merchant Session Key
    public function createMerchantSessionKey(string $vendorName)
    {
        $url = $this->base . '/merchant-session-keys';
        return $this->basicClient()->post($url, ['vendorName' => $vendorName]);
    }

    // Create Transaction (Payment / Refund / etc)
    public function createTransaction(array $payload)
    {
        $url = $this->base . '/transactions';

        // Laravel Http will send JSON when you pass an array as the 2nd param
        $response = $this->basicClient()->post($url, $payload);

        Log::debug('Opayo createTransaction', ['url'=>$url, 'status'=>$response->status(), 'body'=>$response->body()]);

        return $response;
    }

    // Retrieve a transaction
    public function retrieveTransaction(string $transactionId)
    {
        $url = $this->base . '/transactions/' . $transactionId;
        return $this->basicClient()->get($url);
    }
}
