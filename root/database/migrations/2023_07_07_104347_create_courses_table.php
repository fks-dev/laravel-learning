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
        Schema::create('courses', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('title')->comment('タイトル');
            $table->text('introduction')->nullable()->comment('コース紹介');
            $table->text('remarks')->nullable()->comment('備考');
            $table->unsignedInteger('position')->default(0)->comment('ソート番号');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
