<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained('publication_folders')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path'); // storage relative path or external URL
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
