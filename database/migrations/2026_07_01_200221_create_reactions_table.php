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
        Schema::create('reactions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(User::class)->index();
            $table->string('emoji');
            $table->uuidMorphs('reactable');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'emoji', 'reactable_type', 'reactable_id']);
        });
    }
};
