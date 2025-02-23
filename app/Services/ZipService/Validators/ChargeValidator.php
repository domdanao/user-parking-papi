<?php

namespace App\Services\ZipService\Validators;

use App\Services\ZipService\DTOs\ChargeData;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class ChargeValidator
{
    public static function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'amount' => ['required', 'integer', 'min:1'],
            'currency' => [
                'required', 
                'string', 
                'regex:/^[A-Za-z0-9]{2,4}$/'
            ],
            'source' => [
                'required', 
                'string',
                'min:1',
                'regex:/^[A-Za-z0-9\s\'#_\.\-,]+$/',
                'max:255'
            ],
            'description' => [
                'required',
                'string',
                'max:255'
            ],
            'statement_descriptor' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[A-Za-z0-9\s\'#_\.\-,]+$/'
            ],
            'capture' => ['required', 'boolean'],
            'customer' => ['nullable', 'string'],
            'cvc' => [
                'nullable',
                'string',
                'min:3',
                'max:4',
                'regex:/^[0-9]+$/'
            ],
            'require_auth' => ['boolean'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                'Invalid charge data: ' . implode(', ', $validator->errors()->all())
            );
        }
    }
}
