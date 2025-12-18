<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $casts = [
        'schedule' => 'array',
    ];

    protected $fillable = [
        'name',
        'schedule',
        'price',
        'teacher_id',
    ];

    public function sessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
