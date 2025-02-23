<?php

namespace App\Services\ZipCheckoutService\DTOs;

class CheckoutSessionData
{
    public function __construct(
        public readonly string $currency,
        public readonly array $payment_method_types,
        public readonly string $success_url,
        public readonly string $cancel_url,
        public readonly ?string $bank_code = null,
        public readonly ?array $branding = null,
        public readonly ?string $customer = null,
        public readonly ?string $customer_email = null,
        public readonly ?string $customer_name = null,
        public readonly bool $customer_name_collection = false,
        public readonly ?string $customer_phone = null,
        public readonly ?string $description = null,
        public readonly string $locale = 'en',
        public readonly string $mode = 'payment',
        public readonly bool $phone_number_collection = false,
        public readonly ?string $submit_type = null,
        public readonly ?string $client_reference_id = null,
        public readonly ?BillingAddressCollection $billing_address_collection = null,
        public readonly ?ShippingAddressCollection $shipping_address_collection = null,
        public readonly ?array $metadata = null,
        public readonly array $line_items = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            currency: $data['currency'],
            payment_method_types: $data['payment_method_types'],
            success_url: $data['success_url'],
            cancel_url: $data['cancel_url'],
            bank_code: $data['bank_code'] ?? null,
            branding: $data['branding'] ?? null,
            customer: $data['customer'] ?? null,
            customer_email: $data['customer_email'] ?? null,
            customer_name: $data['customer_name'] ?? null,
            customer_name_collection: $data['customer_name_collection'] ?? false,
            customer_phone: $data['customer_phone'] ?? null,
            description: $data['description'] ?? null,
            locale: $data['locale'] ?? 'en',
            mode: $data['mode'] ?? 'payment',
            phone_number_collection: $data['phone_number_collection'] ?? false,
            submit_type: $data['submit_type'] ?? null,
            client_reference_id: $data['client_reference_id'] ?? null,
            billing_address_collection: isset($data['billing_address_collection']) 
                ? BillingAddressCollection::fromArray($data['billing_address_collection'])
                : null,
            shipping_address_collection: isset($data['shipping_address_collection'])
                ? ShippingAddressCollection::fromArray($data['shipping_address_collection'])
                : null,
            metadata: $data['metadata'] ?? null,
            line_items: array_map(
                fn(array $item) => LineItem::fromArray($item),
                $data['line_items'] ?? []
            )
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'currency' => $this->currency,
            'payment_method_types' => $this->payment_method_types,
            'success_url' => $this->success_url,
            'cancel_url' => $this->cancel_url,
            'bank_code' => $this->bank_code,
            'branding' => $this->branding,
            'customer' => $this->customer,
            'customer_email' => $this->customer_email,
            'customer_name' => $this->customer_name,
            'customer_name_collection' => $this->customer_name_collection,
            'customer_phone' => $this->customer_phone,
            'description' => $this->description,
            'locale' => $this->locale,
            'mode' => $this->mode,
            'phone_number_collection' => $this->phone_number_collection,
            'submit_type' => $this->submit_type,
            'client_reference_id' => $this->client_reference_id,
            'billing_address_collection' => $this->billing_address_collection?->toArray(),
            'shipping_address_collection' => $this->shipping_address_collection?->toArray(),
            'metadata' => $this->metadata,
            'line_items' => array_map(fn(LineItem $item) => $item->toArray(), $this->line_items),
        ], fn($value) => !is_null($value) && (!is_array($value) || !empty($value)));
    }
}
