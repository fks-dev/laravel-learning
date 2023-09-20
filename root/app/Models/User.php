<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'mail_address',
    ];

    public function userLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'users_courses', 'user_id', 'course_id');
    }

    // ユーザーが削除された時に、IDに紐づく中間テーブルの値も削除される
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->usersCoursesTable()->delete();
        });
    }

    // users_coursesテーブルとのリレーション
    public function usersCoursesTable()
    {
        return $this->hasMany(UsersCourse::class, 'user_id', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
