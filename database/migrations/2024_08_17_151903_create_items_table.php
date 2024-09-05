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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('products_categories'); // Foreign key to product categories
            $table->string('name'); // Name of the item
            $table->string('item_no')->nullable(); // Optional item number
            $table->json('images')->nullable(); // JSON column for multiple image URLs or paths
            $table->text('item_description')->nullable(); // Optional description of the item
            $table->string('customer_barcode')->nullable()->unique(); // Barcode provided by the customer
            $table->string('customer_ref')->nullable(); // Optional customer reference number
            $table->string('unit_measure')->default('pcs'); // Unit of measurement (default is 'pcs')
            $table->boolean('is_active')->default(true); // Indicates if the item is active
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
