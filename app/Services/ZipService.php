<?php

namespace App\Services;

use App\Services\ZipService\DTOs\ChargeData;
use App\Services\ZipService\DTOs\CustomerData;
use App\Services\ZipService\DTOs\SourceData;
use App\Services\ZipService\Validators\ChargeValidator;
use App\Services\ZipService\Validators\CustomerValidator;
use App\Services\ZipService\Validators\SourceValidator;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ZipService
{
    protected string $baseUrl;
    protected string $publicKey;
    protected string $secretKey;
    protected string $version = 'v2';

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.zip.api_server'), '/');
        $this->publicKey = config('services.zip.pk');
        $this->secretKey = config('services.zip.sk');
        
        if (empty($this->baseUrl)) {
            throw new Exception('Zip API server URL is not configured');
        }

        if (empty($this->publicKey)) {
            throw new Exception('Zip public key is not configured');
        }

        if (empty($this->secretKey)) {
            throw new Exception('Zip secret key is not configured');
        }
    }

    /**
     * Create a new charge
     *
     * @param array $data
     * @return array
     */
    public function createCharge(array $data): array
    {
        ChargeValidator::validate($data);
        $chargeData = ChargeData::fromArray($data);
        
        return $this->post("/charges", $chargeData->toArray());
    }

    /**
     * Retrieve a specific charge
     *
     * @param string $chargeId
     * @return array
     */
    public function getCharge(string $chargeId): array
    {
        return $this->get("/charges/{$chargeId}");
    }

    /**
     * Capture a charge
     *
     * @param string $chargeId
     * @param array $data
     * @return array
     */
    public function captureCharge(string $chargeId, array $data = []): array
    {
        return $this->post("/charges/{$chargeId}/capture", $data);
    }

    /**
     * Refund a charge
     *
     * @param string $chargeId
     * @param array $data
     * @return array
     */
    public function refundCharge(string $chargeId, array $data = []): array
    {
        return $this->post("/charges/{$chargeId}/refund", $data);
    }

    /**
     * Create a new customer
     *
     * @param array $data
     * @return array
     */
    public function createCustomer(array $data): array
    {
        CustomerValidator::validate($data);
        $customerData = CustomerData::fromArray($data);
        
        return $this->post("/customers", $customerData->toArray());
    }

    /**
     * Retrieve a specific customer
     *
     * @param string $customerId
     * @return array
     */
    public function getCustomer(string $customerId): array
    {
        return $this->get("/customers/{$customerId}");
    }

    /**
     * Update a customer
     *
     * @param string $customerId
     * @param array $data
     * @return array
     */
    public function updateCustomer(string $customerId, array $data): array
    {
        CustomerValidator::validate($data);
        $customerData = CustomerData::fromArray($data);
        
        return $this->put("/customers/{$customerId}", $customerData->toArray());
    }

    /**
     * Find customer by email
     *
     * @param string $email
     * @return array
     */
    public function getCustomerByEmail(string $email): array
    {
        return $this->get("/customers/by_email/{$email}");
    }

    /**
     * Create a new payment source
     *
     * @param array $data
     * @return array
     */
    public function createSource(array $data): array
    {
        SourceValidator::validate($data);
        $sourceData = SourceData::fromArray($data);
        
        return $this->post("/sources", $sourceData->toArray());
    }

    /**
     * Retrieve a specific source
     *
     * @param string $sourceId
     * @return array
     */
    public function getSource(string $sourceId): array
    {
        return $this->get("/sources/{$sourceId}");
    }

    /**
     * Attach a source to a customer
     *
     * @param string $customerId
     * @param array $data
     * @return array
     */
    public function attachSourceToCustomer(string $customerId, array $data): array
    {
        SourceValidator::validate($data);
        $sourceData = SourceData::fromArray($data);
        
        return $this->post("/customers/{$customerId}/sources", $sourceData->toArray());
    }

    /**
     * Remove a source from a customer
     *
     * @param string $customerId
     * @param string $sourceId
     * @return array
     */
    public function detachSourceFromCustomer(string $customerId, string $sourceId): array
    {
        return $this->delete("/customers/{$customerId}/sources/{$sourceId}");
    }

    /**
     * Make a GET request to the Zip API
     *
     * @param string $endpoint
     * @param array $query
     * @return array
     */
    protected function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, [], $query);
    }

    /**
     * Make a POST request to the Zip API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Make a PUT request to the Zip API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, $data);
    }

    /**
     * Make a DELETE request to the Zip API
     *
     * @param string $endpoint
     * @return array
     */
    protected function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Make a request to the Zip API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $query
     * @return array
     * @throws Exception
     */
    public function request(string $method, string $endpoint, array $data = [], array $query = []): array
    {
		$fullUrl = "{$this->baseUrl}/{$this->version}{$endpoint}";

        $response = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'query' => $query,
            ])
            ->{strtolower($method)}(
                $fullUrl,
                $data
            );

        if (!$response->successful()) {
            throw new Exception(
                "Zip API error: " . ($response['message'] ?? $response->status()),
                $response->status()
            );
        }

        return $response->json();
    }
}
