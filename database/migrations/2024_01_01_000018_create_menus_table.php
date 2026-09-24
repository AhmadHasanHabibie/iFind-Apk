<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('name');
            $table->string('category')->default('minuman'); // makanan, minuman, snack, paket_hemat, lainnya
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_recommended')->default(false);
            $table->timestamps();

            $table->index(['store_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
