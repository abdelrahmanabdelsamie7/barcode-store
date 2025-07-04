<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_discount_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('visitor_token')->nullable()->index();
            $table->boolean('is_used')->default(false)->index();
            $table->timestamp('used_at')->nullable()->index();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('campaign_id')->constrained('discount_campaigns')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'campaign_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('user_discount_codes');
    }
};
