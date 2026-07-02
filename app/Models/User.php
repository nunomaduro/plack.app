<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property-read string $avatar
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

    private const AVATAR_COLORS = [
        ['color' => 'FFFFFF', 'background' => 'F44336'],
        ['color' => 'FFFFFF', 'background' => 'E91E63'],
        ['color' => 'FFFFFF', 'background' => '9C27B0'],
        ['color' => 'FFFFFF', 'background' => '673AB7'],
        ['color' => 'FFFFFF', 'background' => '3F51B5'],
        ['color' => 'FFFFFF', 'background' => '2196F3'],
        ['color' => 'FFFFFF', 'background' => '009688'],
        ['color' => 'FFFFFF', 'background' => '4CAF50'],
        ['color' => 'FFFFFF', 'background' => 'FF9800'],
        ['color' => 'FFFFFF', 'background' => 'FF5722'],
    ];

    /** @var list<string> */
    protected $appends = ['avatar'];

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
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
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

    /**
     * @return Attribute<string, never>
     */
    protected function avatar(): Attribute
    {
        return Attribute::get(function (): string {
            $colors = self::AVATAR_COLORS[abs(crc32($this->name)) % count(self::AVATAR_COLORS)];

            return 'https://ui-avatars.com/api/?'.http_build_query([
                'name' => $this->name,
                'color' => $colors['color'],
                'background' => $colors['background'],
            ]);
        });
    }
}
