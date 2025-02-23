<?php

namespace App\Services\ZipCheckoutService\DTOs;

class LineItem
{
    public function __construct(
        public readonly string $name,
        public readonly int $amount,
        public readonly string $currency,
        public readonly int $quantity,
        public readonly ?string $description = null,
        public readonly ?array $metadata = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            amount: $data['amount'],
            currency: $data['currency'],
            quantity: $data['quantity'],
            description: $data['description'] ?? null,
            metadata: $data['metadata'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'description' => $this->description,
            'metadata' => $this->metadata,
        ], fn($value) => !is_null($value));
    }
}
