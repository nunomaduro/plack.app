<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateWorkspaceRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique(Workspace::class, 'name')->where('user_id', $this->user()?->id),
            ],
        ];
    }
}
