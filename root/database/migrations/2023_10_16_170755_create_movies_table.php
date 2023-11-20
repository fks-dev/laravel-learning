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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->comment('コースID');
            $table->unsignedBigInteger('user_id')->comment('アップロードユーザー');
            $table->Integer('view')->comment('視聴回数');
            $table->string('title')->comment('タイトル');
            $table->string('url')->comment('URL');
            $table->boolean('public')->default(true)->comment('公開・非公開');
            $table->softDeletes();
            $table->datetimes();

            //外部キー制約
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
