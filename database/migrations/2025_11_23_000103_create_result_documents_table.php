<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('result_documents', function (Blueprint $table) {
            $table->id();
            $table->string('exam'); // e.g. SFNA, PSLE, CSEE, ACSEE, FTNA
            $table->year('year');
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('file_url'); // path to PDF in public/storage or external URL
            $table->timestamps();
            $table->index(['exam','year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_documents');
    }
};
