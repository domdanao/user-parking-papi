<?php

namespace App\Services\ZipService\Validators;

use App\Services\ZipService\DTOs\CustomerData;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CustomerValidator
{
    public static function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'email' => ['required', 'string', 'email'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['string'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                'Invalid customer data: ' . implode(', ', $validator->errors()->all())
            );
        }
    }
}
