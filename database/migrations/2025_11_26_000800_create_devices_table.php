<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // stable identifier from the app
            $table->string('name')->nullable(); // device name / label
            $table->string('platform', 50)->nullable(); // android, ios, web, etc.
            $table->string('model')->nullable(); // device model, e.g. SM-A123F
            $table->string('os_version', 100)->nullable();
            $table->string('app_version', 100)->nullable();
            $table->boolean('is_blocked')->default(false); // if true, app should be blocked
            $table->boolean('maintenance_required')->default(false); // show maintenance message for this device
            $table->boolean('update_required')->default(false); // force or suggest app update
            $table->text('note')->nullable(); // optional admin note about this device
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
