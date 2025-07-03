<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('color_id')->constrained('colors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_main')->default(false);
            $table->unique(['product_id', 'color_id'], 'product_color_unique');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('product_colors');
    }
};
