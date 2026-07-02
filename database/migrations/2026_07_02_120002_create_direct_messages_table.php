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
        Schema::create('direct_messages', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Conversation::class)->index();
            $table->foreignUuidFor(User::class)->index();
            $table->text('body');
            $table->timestamps();
        });
    }
};
