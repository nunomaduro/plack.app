<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WorkspaceInvitation;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Foundation\Http\FormRequest;

final class RespondToWorkspaceInvitationRequest extends FormRequest
{
    public function authorize(#[RouteParameter('invitation')] WorkspaceInvitation $invitation): bool
    {
        return $this->user()?->email === $invitation->email && ! $invitation->isExpired();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }
}
