<?php

declare(strict_types=1);

use App\Enums\ChannelVisibility;
use App\Models\Workspace;
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
        Schema::create('channels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Workspace::class)->index();
            $table->string('name');
            $table->string('visibility')->default(ChannelVisibility::Public->value);
            $table->timestamps();
        });
    }
};
