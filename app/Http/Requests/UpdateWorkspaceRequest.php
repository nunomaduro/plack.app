<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateWorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(#[RouteParameter('workspace')] Workspace $workspace): bool
    {
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
                Rule::unique(Workspace::class, 'name')
                    ->where('user_id', $workspace->user_id)
                    ->ignore($workspace->id),
            ],
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(Workspace::class)->ignore($workspace->id),
            ],
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
