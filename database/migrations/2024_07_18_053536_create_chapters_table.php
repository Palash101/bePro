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
        Schema::create('co_chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users'); 
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('co_courses');  
            $table->enum('type', ['Video','Text']); 
            $table->string('title');         
            $table->text('attachments');
            $table->string('level');
            $table->string('date');
            $table->string('tags');
            $table->string('description')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
