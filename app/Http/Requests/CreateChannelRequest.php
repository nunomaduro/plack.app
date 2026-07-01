<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateChannelRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workspace = $this->route('workspace');
        assert($workspace instanceof Workspace);

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique(Channel::class, 'name')->where('workspace_id', $workspace->id),
            ],
        ];
    }
}
