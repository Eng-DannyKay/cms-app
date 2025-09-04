<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            if (!Schema::hasColumn('page_views', 'utm_source')) {
                $table->string('utm_source')->nullable()->after('platform');
                $table->string('utm_medium')->nullable()->after('utm_source');
                $table->string('utm_campaign')->nullable()->after('utm_medium');
                $table->string('utm_term')->nullable()->after('utm_campaign');
                $table->string('utm_content')->nullable()->after('utm_term');
            }

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->index(['visitor_id', 'created_at']);
                $table->index(['page_id', 'created_at']);
                $table->index(['country', 'created_at']);
                $table->index(['device_type', 'created_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->dropColumn([
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
            ]);

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropIndex('page_views_visitor_id_created_at_index');
                $table->dropIndex('page_views_page_id_created_at_index');
                $table->dropIndex('page_views_country_created_at_index');
                $table->dropIndex('page_views_device_type_created_at_index');
            }
        });
    }
};
