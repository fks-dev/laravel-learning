<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;


class Content extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'admin_id',
        'title',
        'text',
        'youtube_video_id',
        'remarks',
        'is_public',
    ];

    protected $casts = [
        'is_public'    => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function contentsLogs()
    {
        return $this->hasMany(ContentsLog::class);
    }

    protected static function boot()
    {
        parent::boot();

        // 動画かファイルが論理削除されたタイミングでstorageの動画ファイルを削除する
        static::deleting(function ($content) {
            $moviePath = $content->movie_file_path;
            $filePath = $content->document_file_path;
            // 動画ファイルのパスと、storage内の動画が同じ場合に削除
            if ($moviePath && Storage::disk('public')->exists($moviePath)) {
                Storage::disk('public')->delete($moviePath);

                // ファイルのパスと、storage内のファイルが同じ場合に削除
            } elseif ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        });

        // 並び替え時に使用
        static::creating(function ($model) {
            $model->position = Content::max('position') + 1;
        });

    }
}
