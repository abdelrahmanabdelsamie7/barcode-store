<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('image');
            $table->boolean('is_active')->default(true);
            $table->string('size_type')->default('clothes');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};