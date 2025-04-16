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
            $table->text('description');
            $table->string('image_cover');
            $table->string('sku')->nullable()->unique();
            $table->decimal('price_before_discount', 10, 2);
            $table->integer('discount')->default(0);
            $table->decimal('price_after_discount', 10, 2)->storedAs('(price_before_discount - (price_before_discount * discount / 100))');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->foreignUuid('sub_category_id')->constrained('sub_categories')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreignUuid('brand_id')->constrained('brands')->cascadeOnDelete()->cascadeOnDelete();
            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};