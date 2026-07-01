<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserTag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tag_user', function (Blueprint $table): void {
            $table->foreignUuidFor(UserTag::class)->index();
            $table->foreignUuidFor(User::class)->index();
            $table->timestamps();

            $table->primary(['user_tag_id', 'user_id']);
        });
    }
};
