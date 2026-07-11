<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Contracts\Validation\Validator;
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

    /**
     * A workspace must always keep at least one channel, so the last one
     * cannot be deleted.
     */
    protected function withValidator(Validator $validator): void
    {
        $workspace = $this->route('workspace');
        assert($workspace instanceof Workspace);

        $validator->after(function (Validator $validator) use ($workspace): void {
            if ($workspace->channels()->count() <= 1) {
                $validator->errors()->add('channel', __('A workspace must keep at least one channel.'));
            }
        });
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
