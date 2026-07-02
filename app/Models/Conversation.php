<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $id
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Conversation extends Model
{
    /**
     * @use HasFactory<ConversationFactory>
     */
    use HasFactory;

    use HasUuids;

    /**
     * @return BelongsToMany<User, $this, ConversationUser, 'pivot'>
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ConversationUser::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<DirectMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(DirectMessage::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
