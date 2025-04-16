<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_color_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_color_id')->constrained('product_colors')->cascadeOnDelete();
            $table->string('image');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('product_color_images');
    }
};
