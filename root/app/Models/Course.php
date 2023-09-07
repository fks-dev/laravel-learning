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
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
