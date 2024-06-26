<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('live_public_key')->nullable();
            $table->string('test_public_key')->nullable();
            $table->string('live_secret_key')->nullable();
            $table->string('test_secret_key')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->dateTime('activate_at')->nullable();
            $table->dateTime('deactivate_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
