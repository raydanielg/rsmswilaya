<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (!Schema::hasColumn('regions', 'code')) {
                $table->string('code')->nullable()->unique()->after('name');
            }
        });
    }
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
