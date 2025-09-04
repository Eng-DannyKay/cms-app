<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     
        if (Schema::hasTable('themes')) {

            if (!Schema::hasColumn('themes', 'description')) {
                Schema::table('themes', function (Blueprint $table) {
                    $table->text('description')->nullable()->after('colors');
                });
            }
            
            if (!Schema::hasColumn('themes', 'version')) {
                Schema::table('themes', function (Blueprint $table) {
                    $table->string('version')->default('1.0.0')->after('description');
                });
            }
            
            if (!Schema::hasColumn('themes', 'author')) {
                Schema::table('themes', function (Blueprint $table) {
                    $table->string('author')->nullable()->after('version');
                });
            }
            
            if (!Schema::hasColumn('themes', 'preview_image')) {
                Schema::table('themes', function (Blueprint $table) {
                    $table->string('preview_image')->nullable()->after('author');
                });
            }
            
            Schema::table('themes', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->change();
            });
        }
    }

    public function down(): void
    {
        // Remove the added columns if rolling back
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn(['description', 'version', 'author', 'preview_image']);
        });
    }
};