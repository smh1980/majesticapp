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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Foreign key to items table
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // Foreign key to customers table
            $table->decimal('price', 10, 2); // Price of the item for the customer
            $table->timestamps(); // Created and updated timestamps
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
