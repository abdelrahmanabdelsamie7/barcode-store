<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->uuid('visitor_token')->nullable()->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('postal_code')->nullable();
            $table->text('address');
            $table->enum('city', [
                'Cairo',
                'Alexandria',
                'Giza',
                'Sohag',
                'Asyut',
                'Mansoura',
                'Zagazig',
                'Tanta',
                'Banha',
                'Minya',
                'Qena',
                'Other'
            ]);
            $table->decimal('total_price', 10, 2);
            $table->integer('total_quantity');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'])->default('Pending')->index();
            $table->enum('payment_method', ['Cash on Delivery', 'Vodfone Cach', 'Insta Pay', 'mylo'])->default('Cash on Delivery');
            $table->string('payment_phone')->nullable();
            $table->string('payment_reference')->nullable()->index();
            $table->string('payment_proof')->nullable();
            $table->foreignUuid('user_discount_code_id')->nullable()->constrained('user_discount_codes')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
