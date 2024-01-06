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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name');
            $table->string('description');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('pieces')->nullable();
            $table->string('carton')->nullable();
            $table->boolean('featured');
            $table->string('file_path');
            $table->string('price');
            $table->string('product_name');
            $table->string('discount_price')->nullable();
            $table->string('discount_percentage')->nullable();
            $table->boolean('instock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
