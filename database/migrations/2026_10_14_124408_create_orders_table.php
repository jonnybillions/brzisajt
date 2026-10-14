<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->unique();
            $table->string('status')->default('new')->index();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('channel')->nullable();
            $table->json('config');
            $table->json('extras')->nullable();
            $table->text('comment')->nullable();
            $table->string('cloud_link', 2048)->nullable();
            $table->timestamp('consent_at');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
