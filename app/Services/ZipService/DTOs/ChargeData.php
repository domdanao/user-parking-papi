<?php

namespace App\Services\ZipService\DTOs;

class ChargeData
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $source,
        public readonly string $description,
        public readonly string $statement_descriptor,
        public readonly bool $capture = true,
        public readonly ?string $customer = null,
        public readonly ?string $cvc = null,
        public readonly bool $require_auth = true
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            currency: $data['currency'],
            source: $data['source'],
            description: $data['description'],
            statement_descriptor: $data['statement_descriptor'],
            capture: $data['capture'] ?? true,
            customer: $data['customer'] ?? null,
            cvc: $data['cvc'] ?? null,
            require_auth: $data['require_auth'] ?? true
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amount,
            'currency' => $this->currency,
            'source' => $this->source,
            'description' => $this->description,
            'statement_descriptor' => $this->statement_descriptor,
            'capture' => $this->capture,
            'customer' => $this->customer,
            'cvc' => $this->cvc,
            'require_auth' => $this->require_auth,
        ], fn($value) => !is_null($value));
    }
}
