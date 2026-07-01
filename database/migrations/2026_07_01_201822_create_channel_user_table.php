<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channel_user', function (Blueprint $table): void {
            $table->foreignUuidFor(Channel::class);
            $table->foreignUuidFor(User::class);
            $table->string('role');
            $table->timestamps();

            $table->unique(['channel_id', 'user_id']);
        });
    }
};
