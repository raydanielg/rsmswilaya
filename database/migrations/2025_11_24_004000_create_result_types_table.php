<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('result_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // e.g., SFNA, PSLE
            $table->string('name'); // Display name
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('result_type_year', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('result_type_id');
            $table->unsignedBigInteger('result_year_id');
            $table->timestamps();
            $table->unique(['result_type_id','result_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_type_year');
        Schema::dropIfExists('result_types');
    }
};
