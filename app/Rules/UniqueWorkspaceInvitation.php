<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Workspace;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class UniqueWorkspaceInvitation implements ValidationRule
{
    public function __construct(private Workspace $workspace) {}

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        assert(is_string($value));

        if ($this->workspace->owner->email === $value || $this->workspace->members()->where('email', $value)->exists()) {
            $fail('This user already belongs to the workspace.');

            return;
        }

        if ($this->workspace->invitations()->where('email', $value)->exists()) {
            $fail('This user has already been invited to the workspace.');
        }
    }
}
