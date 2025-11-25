<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            if (!Schema::hasColumn('districts', 'code')) {
                $table->string('code')->nullable()->after('name');
                $table->index('code');
            }
        });
    }
    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            if (Schema::hasColumn('districts', 'code')) {
                $table->dropIndex(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
