
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id');
            $table->timestamp('first_visit_at');
            $table->timestamp('last_visit_at');
            $table->integer('page_views_count')->default(0);
            $table->timestamps();

            $table->index(['visitor_id']);
            $table->index(['first_visit_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_sessions');
    }
};
