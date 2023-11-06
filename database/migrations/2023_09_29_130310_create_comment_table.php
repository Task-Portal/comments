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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('commented_comment_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('comments');
            $table->foreign('commented_comment_id')->references('id')->on('comments');
            // $table->string('picture_file_path')->nullable();
            // $table->string('text_file_path')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('level')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
