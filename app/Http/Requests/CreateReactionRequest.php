<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Emoji;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateReactionRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emoji' => [
                'required',
                Rule::enum(Emoji::class),
            ],
        ];
    }
}
