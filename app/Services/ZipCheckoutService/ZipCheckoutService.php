<?php

namespace App\Services\ZipCheckoutService;

use App\Services\ZipService;
use App\Services\ZipCheckoutService\DTOs\CheckoutSessionData;
use App\Services\ZipCheckoutService\DTOs\CheckoutSessionResponse;
use App\Services\ZipCheckoutService\Validators\CheckoutSessionValidator;

class ZipCheckoutService
{
    public function __construct(
        protected readonly ZipService $zipService
    ) {}

    /**
     * Create a new checkout session
     *
     * @param array $data
     * @return CheckoutSessionResponse
     */
    public function createSession(array $data): CheckoutSessionResponse
    {
        CheckoutSessionValidator::validate($data);
        $sessionData = CheckoutSessionData::fromArray($data);
        
        $response = $this->zipService->request('POST', "/sessions", $sessionData->toArray());
        return CheckoutSessionResponse::fromArray($response);
    }

    /**
     * Retrieve a specific checkout session
     *
     * @param string $sessionId
     * @return CheckoutSessionResponse
     */
    public function getSession(string $sessionId): CheckoutSessionResponse
    {
        $response = $this->zipService->request('GET', "/sessions/{$sessionId}");
        return CheckoutSessionResponse::fromArray($response);
    }

    /**
     * Capture a checkout session
     *
     * @param string $sessionId
     * @return CheckoutSessionResponse
     */
    public function captureSession(string $sessionId): CheckoutSessionResponse
    {
        $response = $this->zipService->request('POST', "/sessions/{$sessionId}/capture");
        return CheckoutSessionResponse::fromArray($response);
    }

    /**
     * Expire a checkout session
     *
     * @param string $sessionId
     * @return CheckoutSessionResponse
     */
    public function expireSession(string $sessionId): CheckoutSessionResponse
    {
        $response = $this->zipService->request('POST', "/sessions/{$sessionId}/expire");
        return CheckoutSessionResponse::fromArray($response);
    }

    /**
     * List all checkout sessions
     *
     * @return array<CheckoutSessionResponse>
     */
    public function listSessions(): array
    {
        $response = $this->zipService->request('GET', "/sessions");
        return array_map(
            fn(array $session) => CheckoutSessionResponse::fromArray($session),
            $response['data'] ?? []
        );
    }
}
