<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('result_documents') && !Schema::hasColumn('result_documents','published')) {
            Schema::table('result_documents', function (Blueprint $table) {
                $table->boolean('published')->default(true)->after('file_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('result_documents') && Schema::hasColumn('result_documents','published')) {
            Schema::table('result_documents', function (Blueprint $table) {
                $table->dropColumn('published');
            });
        }
    }
};
