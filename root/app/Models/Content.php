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
        'kind',
        'text',
        'movie',
        'url',
        'file',
        'remarks',
        'public',
        'testTime',
        'testPer',
        'testVol',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // URLの一部だけを格納
    public function setUrlAttribute($value)
    {
        // YouTubeのビデオIDを抽出して格納します
        $videoId = $this->getYouTubeVideoId($value);
        $this->attributes['url'] = $videoId;
    }

    // YouTubeのビデオIDを取得するメソッド
    private function getYouTubeVideoId($url)
    {
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['host']) && $parsedUrl['host'] === 'youtu.be') {
            // youtu.beの場合はパスの部分がビデオIDとなります
            $path = ltrim($parsedUrl['path'], '/');
            return $path;
        } elseif (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $query);
            if (isset($query['v'])) {
                // クエリパラメータにvがある場合もビデオIDとなります
                return $query['v'];
            }
        }

        // ビデオIDが見つからない場合は空文字を返します
        return $url;
    }

    // 動画が論理削除されたタイミングでstorageの動画ファイルを削除する
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($content) {
            $moviePath = $content->movie;
            $filePath = $content->file;
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
