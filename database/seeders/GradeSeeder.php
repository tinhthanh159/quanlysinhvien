<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseClass;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    public function run()
    {
        $courseClasses = CourseClass::all();

        foreach ($courseClasses as $class) {
            foreach ($class->students as $student) {
                $attendanceScore = rand(5, 10);
                $midtermScore = rand(4, 10);
                $finalScore = rand(4, 10);
                $totalScore = ($attendanceScore * 0.1) + ($midtermScore * 0.3) + ($finalScore * 0.6);

                Grade::create([
                    'course_class_id' => $class->id,
                    'student_id' => $student->id,
                    'attendance_score' => $attendanceScore,
                    'midterm_score' => $midtermScore,
                    'final_score' => $finalScore,
                    'total_score' => $totalScore,
                    'status' => 'active',
                ]);
            }
        }
    }
}
