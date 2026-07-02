<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_channel_metadata', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(User::class)->index();
            $table->foreignUuidFor(Channel::class)->index();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('muted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'channel_id']);
        });
    }
};
