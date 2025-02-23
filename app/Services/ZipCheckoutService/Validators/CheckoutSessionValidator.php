<?php

namespace App\Services\ZipCheckoutService\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CheckoutSessionValidator
{
    public static function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'currency' => ['required', 'string', 'size:3'],
            'payment_method_types' => ['required', 'array', 'min:1'],
            'payment_method_types.*' => ['required', 'string', Rule::in([
                'card', 'bpi', 'gcash', 'maya', 'alipay', 'unionpay', 'wechat'
            ])],
            'success_url' => ['required', 'string', 'url'],
            'cancel_url' => ['required', 'string', 'url'],
            'bank_code' => ['nullable', 'string'],
            'branding' => ['nullable', 'array'],
            'customer' => ['nullable', 'string'],
            'customer_email' => ['nullable', 'string', 'email'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_name_collection' => ['boolean'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'locale' => ['string', Rule::in(['en'])],
            'mode' => ['string', Rule::in(['payment', 'setup', 'subscription', 'save_card'])],
            'phone_number_collection' => ['boolean'],
            'submit_type' => ['nullable', 'string', Rule::in(['pay', 'book', 'donate', 'send'])],
            'client_reference_id' => ['nullable', 'string', 'max:255'],
            'billing_address_collection' => ['nullable', 'array'],
            'billing_address_collection.mode' => ['required_with:billing_address_collection', 'string', Rule::in(['auto', 'required'])],
            'shipping_address_collection' => ['nullable', 'array'],
            'shipping_address_collection.mode' => ['required_with:shipping_address_collection', 'string', Rule::in(['auto', 'required'])],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['string'],
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(
                'Invalid checkout session data: ' . implode(', ', $validator->errors()->all())
            );
        }
    }
}
