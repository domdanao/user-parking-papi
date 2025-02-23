<?php

namespace App\Services\ZipCheckoutService\DTOs;

use DateTime;

class CheckoutSessionResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly int $amount_subtotal,
        public readonly int $amount_total,
        public readonly ?string $bank_code,
        public readonly array $branding,
        public readonly ?string $client_reference_id,
        public readonly string $currency,
        public readonly ?string $customer,
        public readonly string $payment_status,
        public readonly string $payment_url,
        public readonly DateTime $created_at,
        public readonly ?DateTime $expired_at = null,
        public readonly array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            object: $data['object'],
            amount_subtotal: $data['amount_subtotal'],
            amount_total: $data['amount_total'],
            bank_code: $data['bank_code'] ?? null,
            branding: $data['branding'] ?? [],
            client_reference_id: $data['client_reference_id'] ?? null,
            currency: $data['currency'],
            customer: $data['customer'] ?? null,
            payment_status: $data['payment_status'],
            payment_url: $data['payment_url'],
            created_at: new DateTime($data['created_at']),
            expired_at: isset($data['expired_at']) ? new DateTime($data['expired_at']) : null,
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'object' => $this->object,
            'amount_subtotal' => $this->amount_subtotal,
            'amount_total' => $this->amount_total,
            'bank_code' => $this->bank_code,
            'branding' => $this->branding,
            'client_reference_id' => $this->client_reference_id,
            'currency' => $this->currency,
            'customer' => $this->customer,
            'payment_status' => $this->payment_status,
            'payment_url' => $this->payment_url,
            'created_at' => $this->created_at->format(DateTime::ATOM),
            'expired_at' => $this->expired_at?->format(DateTime::ATOM),
            'metadata' => $this->metadata,
        ], fn($value) => !is_null($value));
    }
}
