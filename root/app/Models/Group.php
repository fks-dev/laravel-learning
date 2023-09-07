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
        return $this->hasMany(Course::class);
    }

    public function Users()
    {
        return $this->hasMany(User::class);
    }
}
