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
        Schema::create('admins', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->comment('ID');
            $table->char('role')->comment('権限');
            $table->timestamp('deleted_at')->comment('削除日時');
            $table->timestamp('created_at')->comment('作成日時');
            $table->timestamp('updated_at')->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
