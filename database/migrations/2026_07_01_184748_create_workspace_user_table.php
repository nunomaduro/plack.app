<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_user', function (Blueprint $table): void {
            $table->foreignUuidFor(Workspace::class)->index();
            $table->foreignUuidFor(User::class)->index();
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id']);
        });
    }
};
