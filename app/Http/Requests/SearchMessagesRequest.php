<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

final class SearchMessagesRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:1'],
            'channel_id' => ['sometimes', 'string', 'exists:'.Channel::class.',id'],
        ];
    }
}
