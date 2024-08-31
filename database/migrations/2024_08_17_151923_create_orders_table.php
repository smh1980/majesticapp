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
            $table->foreignId('customer_id')->constrained('customers'); // Foreign key to customers table
            $table->foreignId('user_id')->constrained('users'); // Foreign key to users table (sales staff)
            $table->decimal('orders_total_amount', 10, 2); // Total amount of the order
            $table->decimal('vat', 10, 2)->default(0.00); // VAT amount (5% of the total amount)
            $table->decimal('grand_total', 10, 2); // Grand total (orders_total_amount + vat)
            $table->text('remarks')->nullable(); // Optional remarks for the order
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new'); // Status of the order
            $table->timestamps(); // Created and updated timestamps
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
