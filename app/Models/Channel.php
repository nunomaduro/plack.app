<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ChannelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NunoMaduro\LaravelSluggable\Attributes\Sluggable;

/**
 * @property-read string $id
 * @property-read string $workspace_id
 * @property-read string $name
 * @property-read string $slug
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[Sluggable(from: 'name', scope: 'workspace_id', onUpdating: true)]
final class Channel extends Model
{
    /**
     * @use HasFactory<ChannelFactory>
     */
    use HasFactory;

    use HasUuids;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Workspace, $this>
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'workspace_id' => 'string',
            'name' => 'string',
            'slug' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
