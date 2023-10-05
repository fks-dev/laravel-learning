<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_name',
        'remarks',
        'updated_at',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'groups_courses', 'group_id', 'course_id')->withTimestamps();
    }

    public function Users()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id')->withTimestamps();
    }

    // ユーザーが削除された時に、IDに紐づく中間テーブルの値も削除される
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($group) {
            $group->UsersGroupsTable()->delete();
        });

        static::deleting(function ($group) {
            $group->GroupsCoursesTable()->delete();
        });
    }

    // users_groupsテーブルとのリレーション
    public function UsersGroupsTable()
    {
        return $this->hasMany(UsersGroup::class, 'group_id', 'id');
    }

    // groups_coursesテーブルとのリレーション
    public function GroupsCoursesTable()
    {
        return $this->hasMany(GroupsCourse::class, 'group_id', 'id');
    }
}
