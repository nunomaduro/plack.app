<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_user', function (Blueprint $table): void {
            $table->foreignUuidFor(Conversation::class);
            $table->foreignUuidFor(User::class);
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });
    }
};
