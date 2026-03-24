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
        Schema::table('orders', function (Blueprint $table) {
             // rendre customer_id nullable
            $table->unsignedBigInteger('customer_id')->nullable()->change();

            // ajouter une valeur par défaut à status
            $table->string('status')->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
             $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->string('status')->default(null)->change();
        });
    }
};
