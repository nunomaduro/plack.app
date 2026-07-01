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
        Schema::create('workspaces', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }
};
