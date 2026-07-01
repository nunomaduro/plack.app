<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_presets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(User::class)->index();
            $table->string('emoji', 50);
            $table->string('text', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_presets');
    }
};
