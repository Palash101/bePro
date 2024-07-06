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
        Schema::create('pro_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->string('name');
            $table->enum('status', ['Active', 'Block']);
            $table->timestamps();
        });

       

        Schema::create('pro_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('pro_brands');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('pro_categories');
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
            $table->string('variant_name');
            $table->string('image')->nullable();
            $table->string('text')->nullable();
            $table->enum('type', ['Size', 'Color','Normal'])->nullable();
            $table->string('price');
            $table->enum('discount', [true, false])->nullable();
            $table->string('discount_price')->nullable();            
            $table->text('descriptions')->nullable();
            $table->string('current_stock');
            $table->string('upcoming_stock')->nullable();;
            $table->enum('status', ['Active', 'Block']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catgory');
    }
};
