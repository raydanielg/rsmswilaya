<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hamlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('street_id')->constrained('streets')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['street_id','name']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('hamlets');
    }
};
