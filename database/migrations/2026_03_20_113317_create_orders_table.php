<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('order_no')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('card_last_digit')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('booking_date')->nullable();
            $table->string('status')->default('pending');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
