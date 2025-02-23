<?php

namespace App\Services\ZipService\Validators;

use App\Services\ZipService\DTOs\SourceData;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class SourceValidator
{
    public static function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'type' => ['required', 'string', 'in:card'],
            'card' => ['required', 'array'],
            'card.number' => ['required', 'string', 'min:13', 'max:19'],
            'card.exp_month' => ['required', 'integer', 'between:1,12'],
            'card.exp_year' => ['required', 'integer', 'min:2024'],
            'card.cvc' => ['required', 'string', 'size:3'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['string'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                'Invalid source data: ' . implode(', ', $validator->errors()->all())
            );
        }
    }
}
