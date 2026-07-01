<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property-read string $id
 * @property-read string $name
 * @property-read string $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string $password
 * @property-read string|null $remember_token
 * @property-read string|null $two_factor_secret
 * @property-read string|null $two_factor_recovery_codes
 * @property-read CarbonInterface|null $two_factor_confirmed_at
 * @property-read string|null $status_emoji
 * @property-read string|null $status_text
 * @property-read CarbonInterface|null $status_expires_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[Hidden([
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
])]
final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasUuids;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'remember_token' => 'string',
            'two_factor_secret' => 'string',
            'two_factor_recovery_codes' => 'string',
            'two_factor_confirmed_at' => 'datetime',
            'status_emoji' => 'string',
            'status_text' => 'string',
            'status_expires_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<Workspace, $this>
     */
    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return HasMany<StatusPreset, $this>
     */
    public function statusPresets(): HasMany
    {
        return $this->hasMany(StatusPreset::class);
    }

    public function isStatusExpired(): bool
    {
        return $this->status_expires_at !== null && $this->status_expires_at->isPast();
    }

    /**
     * @return array{emoji: string, text: string, expires_at: CarbonInterface|null}|null
     */
    public function currentStatus(): ?array
    {
        if ($this->status_emoji === null && $this->status_text === null) {
            return null;
        }

        if ($this->isStatusExpired()) {
            return null;
        }

        return [
            'emoji' => $this->status_emoji,
            'text' => $this->status_text,
            'expires_at' => $this->status_expires_at,
        ];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }
}
