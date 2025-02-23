<?php

namespace App\Services\ZipCheckoutService\DTOs;

class ShippingAddressCollection
{
    public function __construct(
        public readonly string $mode
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            mode: $data['mode']
        );
    }

    public function toArray(): array
    {
        return [
            'mode' => $this->mode
        ];
    }
}
