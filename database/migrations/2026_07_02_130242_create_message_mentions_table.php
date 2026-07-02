<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_mentions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Message::class)->index();
            $table->foreignUuidFor(User::class)->index();
            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_mentions');
    }
};
