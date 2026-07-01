<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WorkspaceInvitation;
use Illuminate\Foundation\Http\FormRequest;

final class RespondToWorkspaceInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');
        assert($invitation instanceof WorkspaceInvitation);

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
