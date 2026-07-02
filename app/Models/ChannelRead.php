<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ChannelReadFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $channel_id
 * @property-read CarbonInterface $last_read_at
 */
final class ChannelRead extends Model
{
    /**
     * @use HasFactory<ChannelReadFactory>
     */
    use HasFactory;

    use HasUuids;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'channel_id',
        'last_read_at',
    ];

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
        ];
    }
}
