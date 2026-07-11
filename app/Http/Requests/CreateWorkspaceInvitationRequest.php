<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\WorkspaceType;
use App\Models\Workspace;
use App\Rules\UniqueWorkspaceInvitation;
use App\Rules\ValidEmail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\ProhibitedIf;

final class CreateWorkspaceInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $workspace = $this->route('workspace');
        assert($workspace instanceof Workspace);

        return $workspace->user_id === $this->user()?->id;
    }

    /**
     * @return array<string, array<int, ProhibitedIf|ValidationRule|string>>
     */
    public function rules(): array
    {
        $workspace = $this->route('workspace');
        assert($workspace instanceof Workspace);

        return [
            'email' => [
                'required',
                'string',
                Rule::prohibitedIf($workspace->type === WorkspaceType::Public),
                new ValidEmail,
                new UniqueWorkspaceInvitation($workspace),
            ],
        ];
    }
}
