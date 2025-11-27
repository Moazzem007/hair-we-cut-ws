<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpayoService
{
    protected $base;
    protected $auth;

    public function __construct()
    {
        $this->base = rtrim(config('app.opayo_base', env('OPAYO_BASE')), '/');
        $key = env('OPAYO_INTEGRATION_KEY');
        $pass = env('OPAYO_INTEGRATION_PASSWORD');
        $this->auth = base64_encode("{$key}:{$pass}");
    }

    protected function basicAuthHeaders()
    {
        return [
            'Authorization' => 'Basic ' . $this->auth,
            'Content-Type'  => 'application/json',
        ];
    }

    // Create merchant session key
    public function createMerchantSessionKey(string $vendorName)
    {
        $url = $this->base . '/merchant-session-keys';
        $resp = Http::withHeaders($this->basicAuthHeaders())
            ->post($url, ['vendorName' => $vendorName]);

        return $resp->throw()->json();
    }

    // Create transaction (payment / refund / etc)
    public function createTransaction(array $payload)
    {
        $url = $this->base . '/transactions';
        $resp = Http::withHeaders($this->basicAuthHeaders())
            ->post($url, $payload);

        return $resp; // do not throw here; caller will inspect status
    }

    public function retrieveTransaction(string $transactionId)
    {
        $url = $this->base . "/transactions/{$transactionId}";
        $resp = Http::withHeaders($this->basicAuthHeaders())->get($url);
        return $resp->throw()->json();
    }
}
