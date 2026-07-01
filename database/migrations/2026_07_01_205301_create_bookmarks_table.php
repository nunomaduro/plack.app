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
        Schema::create('bookmarks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(User::class)->index();
            $table->foreignUuidFor(Message::class)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'message_id']);
        });
    }
};
