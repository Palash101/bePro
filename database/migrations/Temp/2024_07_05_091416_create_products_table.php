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
        Schema::create('pro_brands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->string('name');
            $table->string('image');
            $table->text('descriptions')->nullable();
            $table->enum('status', ['Active', 'Block']);
            $table->timestamps();
        });

        Schema::create('pro_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('pro_brands');
            $table->string('name');
            $table->string('slug');
            $table->string('featured_image');
            $table->text('descriptions')->nullable();
            $table->enum('status', ['Active', 'Block']);
            $table->timestamps();
        });
        Schema::create('pro_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('pro_products');
            $table->string('name');
            $table->enum('type', ['Size', 'Color']);
            $table->text('descriptions')->nullable();
            $table->enum('status', ['Active', 'Block']);
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
