<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


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

        // 並び替え時に使用
        static::creating(function ($model) {
            $model->position = Content::max('position') + 1;
        });

    }
}
