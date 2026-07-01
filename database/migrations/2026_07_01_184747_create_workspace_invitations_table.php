<?php

declare(strict_types=1);

use App\Models\Workspace;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_invitations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Workspace::class)->index();
            $table->string('code')->unique();
            $table->string('email');
            $table->foreignUuid('invited_by')->index();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['workspace_id', 'email']);
        });
    }
};
