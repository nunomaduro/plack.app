<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WorkspaceTag;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateWorkspaceTagRequest extends FormRequest
{
    public function authorize(#[RouteParameter('workspaceTag')] WorkspaceTag $workspaceTag): bool
    {
        return $workspaceTag->user_id === $this->user()?->id;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tag = $this->route('workspaceTag');
        assert($tag instanceof WorkspaceTag);

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:40',
                Rule::unique(WorkspaceTag::class)->where('user_id', $tag->user_id)->ignore($tag->id),
            ],
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
