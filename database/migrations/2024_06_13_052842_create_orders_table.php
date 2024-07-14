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
            $table->string('code_order')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer');
            $table->string('no_table')->nullable();
            $table->bigInteger('total_price');
            $table->enum('payment_method', ['cash', 'non_cash'])->nullable();
            $table->enum('status', ['pending', 'completed']);
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
