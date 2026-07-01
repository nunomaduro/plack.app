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
     * @return HasMany<Preference, $this>
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(Preference::class);
    }

    /**
     * Create or update one of the user's preferences.
     *
     * Preferences are stored as a per-user key/value pair. When a preference
     * with the given name already exists for the user, its value is updated;
     * otherwise a new preference record is created.
     *
     * @param  string  $name  The preference key (max 60 characters).
     * @param  string  $value  The preference value (max 250 characters).
     * @param  string|null  $defaultValue  Optional default value stored alongside the preference.
     */
    public function updateOrCreatePreference(string $name, string $value, ?string $defaultValue = null): Preference
    {
        return $this->preferences()->updateOrCreate(
            ['name' => $name],
            ['value' => $value, 'default_value' => $defaultValue],
        );
    }

    /**
     * Get the value of one of the user's preferences by name.
     *
     * When the preference exists its stored value is returned. Otherwise the
     * preference's default value is used, falling back to an empty string when
     * neither is available.
     *
     * @param  string  $name  The preference key to read.
     * @return string The resolved preference value.
     */
    public function getPreference(string $name): string
    {
        $preference = $this->preferences()->where('name', $name)->first();

        return $preference?->value ?? $preference?->default_value ?? '';
    }

    /**
     * Delete one of the user's preferences by name.
     *
     * @param  string  $name  The preference key to remove.
     * @return bool True when a matching preference was deleted, false otherwise.
     */
    public function deletePreference(string $name): bool
    {
        return (bool) $this->preferences()->where('name', $name)->delete();
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
