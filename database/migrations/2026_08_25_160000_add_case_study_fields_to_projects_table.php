<?php

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
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('role')->nullable()->after('description');
            $table->text('problem')->nullable()->after('role');
            $table->text('solution')->nullable()->after('problem');
            $table->text('result')->nullable()->after('solution');
            $table->date('started_at')->nullable()->after('repository_url');
            $table->date('finished_at')->nullable()->after('started_at');
            $table->unsignedSmallInteger('sort_order')->default(100)->after('published_at');

            $table->index(['published_at', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropIndex(['published_at', 'sort_order']);

            $table->dropColumn([
                'role',
                'problem',
                'solution',
                'result',
                'started_at',
                'finished_at',
                'sort_order',
            ]);
        });
    }
};
