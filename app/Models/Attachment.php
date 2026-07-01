<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $workspace_id
 * @property-read string $message_id
 * @property-read string $user_id
 * @property-read string $original_filename
 * @property-read string $mime_type
 * @property-read int $size_bytes
 * @property-read string $storage_key
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Attachment extends Model
{
    /**
     * @use HasFactory<AttachmentFactory>
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
     * @return BelongsTo<Message, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'workspace_id' => 'string',
            'message_id' => 'string',
            'user_id' => 'string',
            'original_filename' => 'string',
            'mime_type' => 'string',
            'size_bytes' => 'integer',
            'storage_key' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
