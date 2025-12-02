<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
        'student_id',
        'attendance_score',
        'midterm_score',
        'final_score',
        'other_score',
        'total_score',
        'gpa',
        'status',
        'note'
    ];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function calculateTotal()
    {
        $attendance = $this->attendance_score ?? 0;
        $midterm = $this->midterm_score ?? 0;
        $final = $this->final_score ?? 0;

        // Default weights: 10% - 30% - 60%
        $this->total_score = ($attendance * 0.1) + ($midterm * 0.3) + ($final * 0.6);

        // Convert 10-scale to 4-scale
        if ($this->total_score >= 8.5) $this->gpa = 4.0;
        elseif ($this->total_score >= 8.0) $this->gpa = 3.5;
        elseif ($this->total_score >= 7.0) $this->gpa = 3.0;
        elseif ($this->total_score >= 6.5) $this->gpa = 2.5;
        elseif ($this->total_score >= 5.5) $this->gpa = 2.0;
        elseif ($this->total_score >= 5.0) $this->gpa = 1.5;
        elseif ($this->total_score >= 4.0) $this->gpa = 1.0;
        else $this->gpa = 0.0;

        return $this->total_score;
    }
}
