<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('global_discount_sub_category', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('global_discount_id')->constrained('global_discounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('sub_category_id')->constrained('sub_categories')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('global_discount_sub_category');
    }
};