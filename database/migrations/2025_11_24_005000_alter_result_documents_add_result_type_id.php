<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('result_documents') && !Schema::hasColumn('result_documents','result_type_id')) {
            Schema::table('result_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('result_type_id')->nullable()->after('year');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('result_documents') && Schema::hasColumn('result_documents','result_type_id')) {
            Schema::table('result_documents', function (Blueprint $table) {
                $table->dropColumn('result_type_id');
            });
        }
    }
};
