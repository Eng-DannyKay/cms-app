<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('client_theme')) {
            Schema::create('client_theme', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->onDelete('cascade');
                $table->foreignId('theme_id')->constrained()->onDelete('cascade');
                $table->json('customizations')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamps();

                $table->unique(['client_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_theme');
    }
};