<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'number_of_credits',
        'theory_hours',
        'practice_hours',
        'description',
        'status'
    ];

    public function courseClasses()
    {
        return $this->hasMany(CourseClass::class);
    }
}
