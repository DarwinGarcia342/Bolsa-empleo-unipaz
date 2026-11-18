<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_code',
        'program',
        'semester',
        'phone',
        'about',
        'cv_path',
        'linkedin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
