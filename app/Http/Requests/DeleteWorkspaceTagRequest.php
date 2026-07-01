<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WorkspaceTag;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteWorkspaceTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tag = $this->route('workspaceTag');
        assert($tag instanceof WorkspaceTag);

        return $tag->user_id === $this->user()?->id;
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
