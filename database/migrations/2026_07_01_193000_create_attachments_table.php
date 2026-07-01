<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Workspace::class)->index();
            $table->foreignUuidFor(Message::class)->index();
            $table->foreignUuidFor(User::class)->index();
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('size_bytes');
            $table->string('storage_key');
            $table->timestamps();
        });
    }
};
