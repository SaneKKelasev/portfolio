<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_views', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_hash', 64);
            $table->date('viewed_on');
            $table->timestamp('viewed_at');
            $table->timestamps();

            $table->unique(['project_id', 'visitor_hash', 'viewed_on']);
            $table->index(['viewed_at', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_views');
    }
};
