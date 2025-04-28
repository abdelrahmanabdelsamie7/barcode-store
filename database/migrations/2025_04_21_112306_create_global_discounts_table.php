<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('global_discounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();;
            $table->integer('percentage');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('global_discounts');
    }
};
