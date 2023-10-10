<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'introduction',
        'remarks',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->position = Course::max('position') + 1;
        });


        // コースが削除された時に、IDに紐づく中間テーブルの値も削除される
        parent::boot();
        static::deleting(function ($course) {
            $course->usersCoursesTable()->delete();
            $course->groupsCoursesTable()->delete();
        });

    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_courses', 'course_id', 'user_id');
    }

    // users_coursesテーブルとのリレーション
    public function usersCoursesTable()
    {
        return $this->hasMany(UsersCourse::class, 'course_id', 'id');
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_courses', 'course_id', 'group_id')->withTimestamps();
    }

    // groups_coursesテーブルとのリレーション
    public function groupsCoursesTable()
    {
        return $this->hasMany(GroupsCourse::class, 'course_id', 'id');
    }

}
