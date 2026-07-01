<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateChannelRequest extends FormRequest
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
        $channel = $this->route('channel');
        assert($channel instanceof Channel);

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:80',
                Rule::unique(Channel::class, 'name')
                    ->where('workspace_id', $channel->workspace_id)
                    ->ignore($channel->id),
            ],
        ];
    }

    protected function failedAuthorization(): void
    {
        throw new NotFoundHttpException(); // @codeCoverageIgnore
    }
}
