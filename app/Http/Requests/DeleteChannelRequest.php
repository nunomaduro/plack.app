<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteChannelRequest extends FormRequest
{
    public function authorize(#[RouteParameter('workspace')] Workspace $workspace): bool
    {
        return $workspace->user_id === $this->user()?->id;
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
