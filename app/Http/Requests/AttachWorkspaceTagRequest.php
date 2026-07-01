<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Workspace;
use App\Models\WorkspaceTag;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AttachWorkspaceTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        assert($workspace instanceof Workspace);

        $tag = $this->route('workspaceTag');
        assert($tag instanceof WorkspaceTag);

        return $workspace->user_id === $this->user()?->id
            && $tag->user_id === $this->user()?->id;
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
