<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->string('matrial');
            $table->string('image_cover');
            $table->decimal('price_before_discount', 10, 2)->index();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->foreignUuid('sub_category_id')->constrained('sub_categories')->cascadeOnDelete()->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
