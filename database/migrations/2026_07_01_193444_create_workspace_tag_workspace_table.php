<?php

declare(strict_types=1);

use App\Models\Workspace;
use App\Models\WorkspaceTag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_tag_workspace', function (Blueprint $table): void {
            $table->foreignUuidFor(WorkspaceTag::class)->index();
            $table->foreignUuidFor(Workspace::class)->index();
            $table->timestamps();

            $table->primary(['workspace_tag_id', 'workspace_id']);
        });
    }
};
