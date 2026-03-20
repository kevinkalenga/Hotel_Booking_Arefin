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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('room_id');

            $table->date('checkin_date');
            $table->date('checkout_date');

            $table->integer('adult')->default(1);
            $table->integer('children')->default(0);

            $table->decimal('subtotal', 10, 2);

           

           // Relations
           $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
           $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
