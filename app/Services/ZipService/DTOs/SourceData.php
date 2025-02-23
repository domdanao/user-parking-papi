<?php

namespace App\Services\ZipService\DTOs;

class SourceData
{
    public function __construct(
        public readonly string $type,
        public readonly array $card,
        public readonly ?array $metadata = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            card: $data['card'],
            metadata: $data['metadata'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'card' => $this->card,
            'metadata' => $this->metadata,
        ], fn($value) => !is_null($value));
    }
}
