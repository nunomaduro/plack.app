<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
final class AttachmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'message_id' => Message::factory(),
            'user_id' => User::factory(),
            'original_filename' => $this->faker->word().'.png',
            'mime_type' => 'image/png',
            'size_bytes' => $this->faker->numberBetween(1_000, 1_000_000),
            'storage_key' => 'workspaces/'.$this->faker->uuid().'/attachments/'.$this->faker->uuid().'.png',
        ];
    }
}
