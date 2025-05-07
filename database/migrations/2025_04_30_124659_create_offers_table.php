<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('discount', 5, 2);
            $table->enum('offerable_type', ['product', 'sub_category']);
            $table->uuid('offerable_id');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->timestamps();
            $table->index(['offerable_type', 'offerable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
