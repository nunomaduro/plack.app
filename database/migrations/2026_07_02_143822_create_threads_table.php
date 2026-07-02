<?php

declare(strict_types=1);

use App\Models\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Message::class)->unique();
            $table->timestamps();
        });
    }
};
