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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders'); // Foreign key to orders table
            $table->foreignId('item_id')->constrained('items'); // Foreign key to items table
            $table->integer('quantity'); // Quantity of the item ordered
            $table->decimal('unit_price', 10, 2); // Price per unit of the item
            $table->decimal('vat', 10, 2)->nullable(); // VAT amount for the item
            $table->decimal('total_price', 10, 2); // Total price for the quantity of the item
            $table->timestamps(); // Created and updated timestamps
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
