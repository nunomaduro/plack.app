<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pins', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Channel::class)->index();
            $table->foreignUuidFor(User::class)->index();
            $table->foreignUuidFor(Message::class)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['channel_id', 'message_id']);
        });
    }
};
