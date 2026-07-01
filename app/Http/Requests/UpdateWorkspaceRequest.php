<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateWorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        assert($workspace instanceof Workspace);

        return $workspace->user_id === $this->user()?->id;
    }

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
                Rule::unique(Workspace::class)->where('user_id', $workspace->user_id)->ignore($workspace->id),
            ],
        ];
    }
}
