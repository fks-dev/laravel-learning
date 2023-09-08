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
    ];

    public function groupsCourses()
    {
        return $this->belongsToMany(Course::class, 'groups_courses', 'course_id', 'group_id');
    }

    public function groupsUsers()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'user_id', 'group_id');
    }
}
