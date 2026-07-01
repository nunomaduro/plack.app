<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\UserTag;
use App\Models\Workspace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CreateUserTagRequest extends FormRequest
{
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
                'min:2',
                'max:40',
                Rule::unique(UserTag::class)->where('workspace_id', $workspace->id),
            ],
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
