<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\EarlyAccessEmail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateEarlyAccessEmailRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique(EarlyAccessEmail::class, 'email'),
            ],
        ];
    }
}
