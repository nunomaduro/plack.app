<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\UserChannelMetadataFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $channel_id
 * @property-read CarbonInterface|null $last_read_at
 * @property-read CarbonInterface|null $muted_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
#[Table(name: 'user_channel_metadata')]
final class UserChannelMetadata extends Model
{
    /**
     * @use HasFactory<UserChannelMetadataFactory>
     */
    use HasFactory;

    use HasUuids;

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'channel_id' => 'string',
            'last_read_at' => 'datetime',
            'muted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
