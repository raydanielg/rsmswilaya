<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('streets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['ward_id','name']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('streets');
    }
};
