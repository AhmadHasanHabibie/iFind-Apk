<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity');
            $table->integer('booked_seats')->default(0);
            $table->enum('status', ['available', 'full', 'closed'])->default('available');
            $table->timestamps();

            $table->unique(['store_id', 'date', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
