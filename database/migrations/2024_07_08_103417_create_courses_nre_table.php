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
        Schema::create('co_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->string('name');
            $table->enum('status', ['Active', 'Block']);
            $table->timestamps();
        });

        Schema::create('co_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('cou_categories');            
            $table->string('title');
            $table->string('slug');
            $table->string('featured_image');
            $table->text('descriptions')->nullable();
            $table->enum('payment_type', ['Free', 'Paid']);
            $table->integer('price')->nullable();
            $table->enum('discount', [true, false]);
            $table->integer('discount_price')->nullable();
            $table->enum('status', ['Active', 'Block']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses_nre');
    }
};
