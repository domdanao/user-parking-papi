<?php

namespace App\Services\ZipService\DTOs;

class CustomerData
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $phone = null,
        public readonly ?array $metadata = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            phone: $data['phone'] ?? null,
            metadata: $data['metadata'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'metadata' => $this->metadata,
        ], fn($value) => !is_null($value));
    }
}
