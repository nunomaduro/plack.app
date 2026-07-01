<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WorkspaceTag;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateWorkspaceTagRequest extends FormRequest
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
                'min:2',
                'max:40',
                Rule::unique(WorkspaceTag::class)->where('user_id', $this->user()?->id),
            ],
        ];
    }
}
