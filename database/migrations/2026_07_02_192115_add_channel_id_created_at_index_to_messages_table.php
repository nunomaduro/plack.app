<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->index(['channel_id', 'created_at']);
            // Redundant now: the composite index above already covers channel_id lookups via its leftmost column.
            $table->dropIndex('messages_channel_id_index');
        });
    }
};
