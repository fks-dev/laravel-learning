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
        'content_type',
        'text',
        'youtube_video_id',
        'movie_file_path',
        'document_file_path',
        'remarks',
        'is_public',
        'time_limit_minutes',
        'passing_score_rate',
        'amount_questions',
    ];

    protected $casts = [
        'content_type' => 'integer',
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

    public function displayType(){
        $content_type = [
            1 => 'ラベル',
            2 => '動画',
            3 => '配布資料',
            4 => 'テキスト',
            5 => '動画'
        ];
        return $content_type[$this->content_type];
    }
    public function getLog($user){
        $content_id = $this->id;
        return ContentsLog::where('user_id',$user->id)->where('content_id',$content_id)->get();
    }
}
