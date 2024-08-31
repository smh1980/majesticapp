<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->string('customer_barcode')->nullable();
            $table->string('customer_ref')->nullable();
        });
    }

    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('customer_barcode');
            $table->dropColumn('customer_ref');
        });
    }
};
