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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->comment('コースID');
            $table->unsignedBigInteger('admin_id')->comment('管理者ID');
            $table->char('title')->comment('タイトル');
            $table->unsignedTinyInteger('content_type')->comment('コンテンツ種別:1:ラベル 2:YouTubeURL 3:配布資料 4:リッチテキスト 5:動画 6:テスト');
            $table->text('text')->nullable()->comment('リッチテキスト');
            $table->char('url')->nullable()->comment('URL');
            $table->string('movie')->nullable()->comment('動画');
            $table->string('file')->nullable()->comment('配布資料');
            $table->integer('testTime')->nullable()->comment('テスト制限時間');
            $table->integer('testPer')->nullable()->comment('合格得点率');
            $table->integer('testVol')->nullable()->comment('出題数');
            $table->text('remarks')->nullable()->comment('備考');
            $table->boolean('public')->comment('公開・非公開');
            $table->unsignedSmallInteger('position')->default(0)->comment('ソート番号');
            $table->timestamp('deleted_at')->nullable()->comment('削除日時');
            $table->timestamp('created_at')->comment('削除日時');
            $table->timestamp('updated_at')->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
