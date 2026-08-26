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
        Schema::table('project_images', function (Blueprint $table) {
            $table->string('large_path', 2048)->nullable()->after('path');
            $table->string('card_path', 2048)->nullable()->after('large_path');
            $table->string('thumb_path', 2048)->nullable()->after('card_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn([
                'large_path',
                'card_path',
                'thumb_path',
            ]);
        });
    }
};
