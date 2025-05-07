<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_color_id')->constrained('product_colors')->cascadeOnDelete();
            $table->foreignUuid('size_id')->constrained('sizes')->cascadeOnDelete();
            $table->integer('quantity');
            $table->unique(['product_color_id', 'size_id'], 'unique_product_color_size');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};