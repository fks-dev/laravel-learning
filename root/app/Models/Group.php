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

    public function Courses()
    {
        return $this->belongsToMany(Course::class, 'groups_courses', 'group_id', 'course_id');
    }

    public function Users()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id');
    }
}
