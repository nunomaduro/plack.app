<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChannelVisibility;
use Carbon\CarbonInterface;
use Database\Factories\ChannelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $workspace_id
 * @property-read string $name
 * @property-read ChannelVisibility $visibility
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Channel extends Model
{
    /**
     * @use HasFactory<ChannelFactory>
     */
    use HasFactory;

    use HasUuids;

    /**
     * @return BelongsTo<Workspace, $this>
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
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
            'visibility' => ChannelVisibility::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
